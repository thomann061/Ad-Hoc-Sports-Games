<?php
/**
 * Created by PhpStorm.
 * User: jthomann
 * Date: 12/6/16
 * Time: 9:16 PM
 */

namespace AdHocSportsGames\Controller;

use AdHocSportsGames\DataAccess\DatabaseConnection;
use AdHocSportsGames\SQL\UserSQL;
use Interop\Container\ContainerInterface;
use AdHocSportsGames\Utilities\JsonWrapper;

/**
 * Class UserController
 * @package Controller
 */
class UserController {

    protected $ci;

    //Constructor
    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
    }

    /**
     * Get all the users
     * @param $args
     * @return JsonWrapper
     */
    public function handleGetUsers($request, $response, $args) {
        $wrapper = (new UserSQL(new DatabaseConnection()))->selectAll();
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }

    /**
     * Get a single user
     * @param $args
     * @return JsonWrapper
     */
    public function handleGetUser($request, $response, $args) {
        $wrapper = (new UserSQL(new DatabaseConnection()))->select($args['id']);
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }

    /**
     * Post a single user
     * @param $args
     * @return JsonWrapper
     */
    public function handlePostUser($request, $response, $args) {
        $wrapper = (new UserSQL(new DatabaseConnection()))->insert($request->getBody());
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }

    /**
     * Put a single user
     * @param $args
     * @return JsonWrapper
     */
    public function handlePutUser($request, $response, $args) {
        $wrapper = (new UserSQL(new DatabaseConnection()))->fullyUpdate($request->getBody(), $args['id']);
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }

    /**
     * Patch a single user
     * @param $args
     * @return JsonWrapper
     */
    public function handlePatchUser($request, $response, $args) {
        $wrapper = (new UserSQL(new DatabaseConnection()))->partiallyUpdate($request->getBody(), $args['id']);
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }

    /**
     * Delete a single user
     * @param $args
     * @return JsonWrapper
     */
    public function handleDeleteUser($request, $response, $args) {
        $wrapper = (new UserSQL(new DatabaseConnection()))->delete($args['id']);
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }
}