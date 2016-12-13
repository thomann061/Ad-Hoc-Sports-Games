# Ad Hoc Sports Games RESTful API

This application uses the Slim Framework for PHP.

The idea of this RESTful API is to help users post a location and time for a sports game of their choice.  Other users will be able to comment on the posted game and if enough people decide they can make it, the game will played.  Afterwards, users can rate the game played.

## Run the Application

Easiest way to run the app locally from a php server:

    php -S 0.0.0.0:8080 -t public public/index.php
    
## Database

Feel free to use the sample database located in /database.txt

## Endpoint/Sub-Endpoint Background

Base URL: http://localhost:8080/api/v1/

There are four endpoints: user, game, comment (sub-endpoint), rating (sub-endpoint)

These four endpoints support six HTTP methods: GET (all), GET (one), POST, PATCH, PUT, DELETE

## Endpoint Format

GET (all): http://localhost:8080/api/v1/{endpoint}

GET (one): http://localhost:8080/api/v1/{endpoint}/{id}

POST: http://localhost:8080/api/v1/{endpoint}/{id}

PATCH: http://localhost:8080/api/v1/{endpoint}/{id}

PUT: http://localhost:8080/api/v1/{endpoint}/{id}

DELETE: http://localhost:8080/api/v1/{endpoint}/{id}

## Sub-Endpoint Format

GET (all): http://localhost:8080/api/v1/{endpoint}/{id}/{sub-endpoint}

GET (one): http://localhost:8080/api/v1/{endpoint}/{id}/{sub-endpoint}/{id}

POST: http://localhost:8080/api/v1/{endpoint}/{id}/{sub-endpoint}

PATCH: http://localhost:8080/api/v1/{endpoint}/{id}/{sub-endpoint}/{id}

PUT: http://localhost:8080/api/v1/{endpoint}/{id}/{sub-endpoint}/{id}

DELETE: http://localhost:8080/api/v1/{endpoint}/{id}/{sub-endpoint}/{id}
