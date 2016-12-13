<?php
// Routes

/**
 * Start Routes
 */

//namespace
$namespace = "AdHocSportsGames";

//versioning
$version = 1;
$verPath = "v" . $version . "/";
$apiPath = "/api/" . $verPath;

//put top level controllers here
$controllers = array("Game", "User");

foreach($controllers as $controller) {
    $lController = strtolower($controller);
    $app->get($apiPath . $lController, $namespace . "\\Controller\\" . $controller . "Controller:handleGetAll");
    $app->get($apiPath . $lController . '/{id}', $namespace . "\\Controller\\" . $controller . "Controller:handleGet");
    $app->post($apiPath . $lController, $namespace . "\\Controller\\" . $controller . "Controller:handlePost");
    $app->delete($apiPath . $lController . '/{id}', $namespace . "\\Controller\\" . $controller . "Controller:handleDelete");
    $app->put($apiPath . $lController . '/{id}', $namespace . "\\Controller\\" . $controller . "Controller:handlePut");
    $app->patch($apiPath . $lController . '/{id}', $namespace . "\\Controller\\" . $controller . "Controller:handlePatch");
}

//Child level routes for Game
$parent = "Game";
$childControllers = array("Rating");

foreach($childControllers as $controller) {
    $lParent = strtolower($parent);
    $lController = strtolower($controller);
    $app->get($apiPath . $lParent . '/{pId}/' . $lController, $namespace . "\\Controller\\" . $controller . "Controller:handleGetAll");
    $app->get($apiPath . $lParent . '/{pId}/' . $lController . '/{cId}', $namespace . "\\Controller\\" . $controller . "Controller:handleGet");
    $app->post($apiPath . $lParent . '/{pId}/' . $lController, $namespace . "\\Controller\\" . $controller . "Controller:handlePost");
    $app->delete($apiPath . $lParent . '/{pId}/' . $lController . '/{cId}', $namespace . "\\Controller\\" . $controller . "Controller:handleDelete");
    $app->put($apiPath . $lParent . '/{pId}/' . $lController . '/{cId}', $namespace . "\\Controller\\" . $controller . "Controller:handlePut");
    $app->patch($apiPath . $lParent . '/{pId}/' . $lController . '/{cId}', $namespace . "\\Controller\\" . $controller . "Controller:handlePatch");
}

/**
 * End Routes
 */