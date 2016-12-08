<?php
/**
 * Created by PhpStorm.
 * User: jthomann
 * Date: 12/8/16
 * Time: 9:17 AM
 */

namespace AdHocSportsGames\Controller;


interface ControllerInterface {
    public function handleGetAll($request, $response, $args);
    public function handleGet($request, $response, $args);
    public function handlePost($request, $response, $args);
    public function handlePut($request, $response, $args);
    public function handlePatch($request, $response, $args);
    public function handleDelete($request, $response, $args);
}