<?php
// Routes

/**
 * Start User Routes
 */

$app->get('/user', 'AdHocSportsGames\Controller\UserController:handleGetUsers');
$app->get('/user/{id}', 'AdHocSportsGames\Controller\UserController:handleGetUser');
$app->post('/user', 'AdHocSportsGames\Controller\UserController:handlePostUser');
$app->delete('/user/{id}', 'AdHocSportsGames\Controller\UserController:handleDeleteUser');
$app->put('/user/{id}', 'AdHocSportsGames\Controller\UserController:handlePutUser');
$app->patch('/user/{id}', 'AdHocSportsGames\Controller\UserController:handlePatchUser');

/**
 * End User Routes
 */