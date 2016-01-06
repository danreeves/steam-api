# Steam API

```sh
git clone https://github.com/danreeves/steam-api
docker-compose build
docker-compose up
```

For local use create a `.env` file like:

```
STEAM_WEB_API_KEY=XXXXXXXXXXXXXXX
STEAM_USER_ID=XXXXXXXXXXXXXXXXXXX
NODE_ENV=development
```

For deployment, set the same variables in the environment.


## Routes

    /api/stats
    /api/friends
    /api/games/owned
    /api/games/recent
    /api/game/{id}/stats
    /api/game/{id}/achievements
