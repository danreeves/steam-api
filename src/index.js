import Hapi from 'hapi';
import Steam from 'steam-web';

var steam = new Steam({
    apiKey: process.env.STEAM_WEB_API_KEY,
    format: 'json',
});

const server = new Hapi.Server({
    connections: {
        router: {
            stripTrailingSlash: true,
        },
    },
});
server.connection({
    port: process.env.PORT || 3000,
});
server.start(() => console.log('Server running at:', server.info.uri));

// /api/stats
server.route({
    method: 'GET',
    path: '/api/stats',
    handler: (request, reply) => {
        return reply(new Promise((resolve, reject) => {
            steam.getPlayerSummaries({
                steamids: [process.env.STEAM_USER_ID],
                callback: function cb (err, data) {
                    if (err) {
                        reject(err);
                    }
                    resolve(data);
                },
            });
        }));
    },
});

// /api/friends
server.route({
    method: 'GET',
    path: '/api/friends',
    handler: (request, reply) => {
        return reply(new Promise((resolve, reject) => {
            steam.getFriendList({
                steamid: process.env.STEAM_USER_ID,
                relationship: 'all',
                callback: function cb (err, data) {
                    if (err) {
                        reject(err);
                    }
                    resolve(data);
                },
            });
        }));
    },
});

// /api/games/owned
server.route({
    method: 'GET',
    path: '/api/games/owned',
    handler: (request, reply) => {
        return reply(new Promise((resolve, reject) => {
            steam.getOwnedGames({
                steamid: process.env.STEAM_USER_ID,
                callback: function cb (err, data) {
                    if (err) {
                        reject(err);
                    }
                    resolve(data);
                },
            });
        }));
    },
});

// /api/games/recent
server.route({
    method: 'GET',
    path: '/api/games/recent',
    handler: (request, reply) => {
        return reply(new Promise((resolve, reject) => {
            steam.getRecentlyPlayedGames({
                steamid: process.env.STEAM_USER_ID,
                callback: function cb (err, data) {
                    if (err) {
                        reject(err);
                    }
                    resolve(data);
                },
            });
        }));
    },
});

// /api/game/{id}/stats
server.route({
    method: 'GET',
    path: '/api/game/{id}/stats',
    handler: (request, reply) => {
        return reply(new Promise((resolve, reject) => {
            steam.getUserStatsForGame({
                steamid: process.env.STEAM_USER_ID,
                appid: request.params.id,
                callback: function cb (err, data) {
                    if (err) {
                        reject(err);
                    }
                    resolve(data);
                },
            });
        }));
    },
});

// /api/game/{id}/achievements
server.route({
    method: 'GET',
    path: '/api/game/{id}/achievements',
    handler: (request, reply) => {
        return reply(new Promise((resolve, reject) => {
            steam.getPlayerAchievements({
                steamid: process.env.STEAM_USER_ID,
                appid: request.params.id,
                l: 'en',
                callback: function cb (err, data) {
                    if (err) {
                        reject(err);
                    }
                    resolve(data);
                },
            });
        }));
    },
});
