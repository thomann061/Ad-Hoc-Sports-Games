<?php
/**
 * Created by PhpStorm.
 * User: jthomann
 * Date: 12/13/16
 * Time: 11:22 AM
 */

namespace AdHocSportsGames\Controller;
use AdHocSportsGames\DataAccess\DatabaseConnection;
use AdHocSportsGames\SQL\CommentSQL;
use AdHocSportsGames\Utilities\JsonWrapper;
use Interop\Container\ContainerInterface;


/**
 * Class CommentController
 * @package Controller
 */
class CommentController implements ControllerInterface {

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
    public function handleGetAll($request, $response, $args) {
        $wrapper = (new CommentSQL(new DatabaseConnection()))->selectAll($args['pId']);
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }

    /**
     * Get a single user
     * @param $args
     * @return JsonWrapper
     */
    public function handleGet($request, $response, $args) {
        $wrapper = (new CommentSQL(new DatabaseConnection()))->select($args['pId'], $args['cId']);
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }

    /**
     * Post a single user
     * @param $args
     * @return JsonWrapper
     */
    public function handlePost($request, $response, $args) {
        $wrapper = (new CommentSQL(new DatabaseConnection()))->insert($args['pId'], $request->getBody());
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }

    /**
     * Put a single user
     * @param $args
     * @return JsonWrapper
     */
    public function handlePut($request, $response, $args) {
        $wrapper = (new CommentSQL(new DatabaseConnection()))->fullyUpdate($args['pId'], $args['cId'], $request->getBody());
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }

    /**
     * Patch a single user
     * @param $args
     * @return JsonWrapper
     */
    public function handlePatch($request, $response, $args) {
        $wrapper = (new CommentSQL(new DatabaseConnection()))->partiallyUpdate($args['pId'], $args['cId'], $request->getBody());
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }

    /**
     * Delete a single user
     * @param $args
     * @return JsonWrapper
     */
    public function handleDelete($request, $response, $args) {
        $wrapper = (new CommentSQL(new DatabaseConnection()))->delete($args['pId'], $args['cId']);
        return $response->withJson($wrapper->toArray(), $wrapper->getCode());
    }
}