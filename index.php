<?php
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

$app->group('/ISteamUser', function () use ($app) {

    $app->group('/GetPlayerSummaries', function () use ($app) {

        $app->get('/v0002/', function () use ($app) {
            get($app, '/ISteamUser/GetPlayerSummaries/v0002/');
        });

    });

    $app->group('/GetFriendList', function () use ($app) {

        $app->get('/v0001/', function () use ($app) {
            get($app, '/ISteamUser/GetFriendList/v0001/');
        });

    });

});

/********************************************************************************
* ISteamUserStats
*******************************************************************************/

$app->group('/ISteamUserStats', function () use ($app) {

    $app->group('/GetUserStatsForGame', function () use ($app) {

        $app->get('/v0002/', function () use ($app) {
            get($app, '/ISteamUserStats/GetUserStatsForGame/v0002/');
        });

    });

    $app->group('/GetPlayerAchievements', function () use ($app) {

        $app->get('/v0001/', function () use ($app) {
            get($app, '/ISteamUserStats/GetPlayerAchievements/v0001/');
        });

    });

});

/********************************************************************************
* IPlayerService
*******************************************************************************/

$app->group('/IPlayerService', function () use ($app) {

    $app->group('/GetOwnedGames', function () use ($app) {

        $app->get('/v0001/', function () use ($app) {
            get($app, '/IPlayerService/GetOwnedGames/v0001/');
        });

    });

    $app->group('/GetRecentlyPlayedGames', function () use ($app) {

        $app->get('/v0001/', function () use ($app) {
            get($app, '/IPlayerService/GetRecentlyPlayedGames/v0001/');
        });

    });

});


$app->run();
