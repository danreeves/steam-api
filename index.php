<?php
error_reporting(E_ERROR | E_PARSE);
/**
 * Steam Web PHP API
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
require 'lib/Slim/Slim.php';
require 'lib/Unirest.php';
require 'steamwebapi_config.php';

const STEAM_WEB_API_BASE_URL = 'http://api.steampowered.com';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->setName('Steam Web API');

// Do nothing when we index the Steam Web PHP API
$app->get(
    '/',
    function () use ($app) {
        $app->halt(403);
    }
);

// Do nothing when we don't find an API endpoint
$app->notFound(function () {
});

function get($app, $endpoint)
{
    $parameters = [
        'key' => STEAM_WEB_API_KEY,
        'steamid' => STEAM_USER_ID,
        'steamids' => STEAM_USER_ID,
    ];
    foreach ($app->request->get() as $key => $value) {
        $parameters[$key] = $value;
    }

    $response = Unirest::get(STEAM_WEB_API_BASE_URL . $endpoint,
                             NULL,
                             $parameters);

    $app->response->setStatus($response->code);
    foreach ($response->headers as $key => $value) {
    	if ($key === 'Content-Encoding') {
    		continue;
    	}
        $app->response->headers->set($key, $value);
    }
    echo $response->raw_body;
}

/********************************************************************************
* ISteamUser
*******************************************************************************/

$app->group('/api', function () use ($app) {

    $app->get('/stats', function () use ($app) {
        get($app, '/ISteamUser/GetPlayerSummaries/v0002/');
    });

    $app->get('/friends', function () use ($app) {

        get($app, '/ISteamUser/GetFriendList/v0001/');

    });

    $app->group('/games', function () use ($app) {

        $app->get('/owned/', function () use ($app) {
            get($app, '/IPlayerService/GetOwnedGames/v0001/?include_appinfo=1');
        });

        $app->get('/recent/', function () use ($app) {
            get($app, '/IPlayerService/GetRecentlyPlayedGames/v0001/?include_appinfo=1');
        });

    });

    $app->group('/game', function() use ($app) {

        $app->get('/:id/achievements/', function ($id) use ($app) {
            get($app, '/ISteamUserStats/GetPlayerAchievements/v0001/?appid='.$id);
        });

        $app->get('/:id/stats/', function ($id) use ($app) {
            get($app, '/ISteamUserStats/GetUserStatsForGame/v0002/?appid='.$id);
        });

    });

});


$app->run();
