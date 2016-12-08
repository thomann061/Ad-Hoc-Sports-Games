<?php
// Routes

/**
 * Start User Routes
 */

//put first name of controller here
$controllers = array("Game", "User");

foreach($controllers as $controller) {
    $app->get('/' . strtolower($controller), "AdHocSportsGames\\Controller\\" . $controller . "Controller:handleGetAll");
    $app->get('/' . strtolower($controller) . '/{id}', "AdHocSportsGames\\Controller\\" . $controller . "Controller:handleGet");
    $app->post('/' . strtolower($controller), "AdHocSportsGames\\Controller\\" . $controller . "Controller:handlePost");
    $app->delete('/' . strtolower($controller) . '/{id}', "AdHocSportsGames\\Controller\\" . $controller . "Controller:handleDelete");
    $app->put('/' . strtolower($controller) . '/{id}', "AdHocSportsGames\\Controller\\" . $controller . "Controller:handlePut");
    $app->patch('/' . strtolower($controller) . '/{id}', "AdHocSportsGames\\Controller\\" . $controller . "Controller:handlePatch");
}

/**
 * End User Routes
 */