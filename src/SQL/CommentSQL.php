<?php
/**
 * Created by PhpStorm.
 * User: jthomann
 * Date: 12/13/16
 * Time: 11:23 AM
 */

namespace AdHocSportsGames\SQL;


use AdHocSportsGames\DataAccess\DatabaseInterface;
use AdHocSportsGames\Http\StatusCodes;
use AdHocSportsGames\Model\Comment;
use AdHocSportsGames\Utilities\JsonWrapper;
use AdHocSportsGames\Utilities\Status;
use DateTime;
use PDO;

class CommentSQL implements SQLChildInterface, JsonResponseInterface {
    private $db;
    private $parent;
    private $child;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db->getInstance();
        $this->parent = "Game";
        $this->child = "Comment";
    }

    /**
     * Json Return Response
     * @param $message
     * @param $data
     * @param $status
     * @param $code
     * @return JsonWrapper
     */
    public function returnResponse($message, $data, $status, $code) {
        $wrapper = new JsonWrapper();
        $wrapper->setMessage($message);
        $wrapper->setData($data);
        $wrapper->setStatus($status);
        $wrapper->setCode($code);
        return $wrapper;
    }

    /**
     * Checks if parent id exists
     * @param $pId
     * @return mixed
     */
    private function checkParent($pId) {
        //Prepare SQL
        $sth = $this->db->prepare("SELECT * FROM " . $this->parent .
            " WHERE GameId = " . $pId); //create statement
        $sth->execute();  //execute the statement on the database

        return $sth->rowCount();
    }

    public function selectAll($pId) {
        //sanitize parent id
        $pId = filter_var($pId, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($pId, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->parent . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check parent
        if(!$this->checkParent($pId))
            return $this->returnResponse($this->parent . "Id does not exist", null, Status::NO_RESULTS, StatusCodes::NOT_FOUND);

        //Prepare SQL
        $sth = $this->db->prepare("SELECT * FROM " . $this->child .
            " WHERE GameId = " . $pId); //create statement
        $sth->execute();  //execute the statement on the database

        //fetch directly into class
        $data = $sth->fetchAll(PDO::FETCH_CLASS, "AdHocSportsGames\\Model\\" . $this->child);

        if($sth->rowCount())
            return $this->returnResponse($this->child . "s were retrieved", $data, Status::SUCCESS, StatusCodes::OK);
        else
            return $this->returnResponse("No " . $this->child . "s exist", null, Status::NO_RESULTS, StatusCodes::NOT_FOUND);
    }

    public function select($pId, $cId) {
        //sanitize parent and child ids
        $pId = filter_var($pId, FILTER_SANITIZE_STRING);
        $cId = filter_var($cId, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($pId, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->parent . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);
        if(!filter_var($cId, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->child . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check parent
        if(!$this->checkParent($pId))
            return $this->returnResponse($this->parent . "Id does not exist", null, Status::NO_RESULTS, StatusCodes::NOT_FOUND);

        //Prepare SQL
        $sth = $this->db->prepare("SELECT * FROM " . $this->child .
            " WHERE " . $this->child . "Id = :id"); //create statement
        $sth->bindParam(':id', $cId);
        $sth->execute();  //execute the statement on the database

        //fetch directly into class
        $data = $sth->fetchAll(PDO::FETCH_CLASS, "AdHocSportsGames\\Model\\" . $this->child);

        if($sth->rowCount())
            return $this->returnResponse($this->child . " was retrieved", $data, Status::SUCCESS, StatusCodes::OK);
        else
            return $this->returnResponse($this->child . " does not exist", null, Status::NO_RESULTS, StatusCodes::NOT_FOUND);
    }

    public function insert($pId, $obj) {
        //vars
        $arr = null;
        $data = null;

        //sanitize parent id
        $pId = filter_var($pId, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($pId, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->parent . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check if valid json
        if(!json_decode($obj))
            $this->returnResponse("JSON format is incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);
        else
            $arr = json_decode($obj, true);

        //check if valid keys exist
        if(array_key_exists("CommentPost", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value))
                    return $this->returnResponse("No values can be empty", null, Status::ERROR, StatusCodes::BAD_REQUEST);
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create
            $data = new Comment();
            $date = new DateTime();
            $data->CommentDateTime = $date->format('Y-m-d H:i:s');
            $data->GameId = $pId;
            $data->CommentPost = $arr['CommentPost'];
        } else
            return $this->returnResponse("Key names are incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check parent
        if(!$this->checkParent($pId))
            return $this->returnResponse($this->parent . "Id does not exist", null, Status::NO_RESULTS, StatusCodes::NOT_FOUND);

        //Prepare SQL
        $sth = $this->db->prepare("INSERT INTO " . $this->child . " (CommentPost, CommentDateTime, GameId) 
                                    VALUES(:commentPost, :commentDateTime, :gameId)"); //create statement
        $sth->bindParam(':commentPost', $data->CommentPost);
        $sth->bindParam(':commentDateTime', $data->CommentDateTime);
        $sth->bindParam(':gameId', $data->GameId);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount())
            return $this->returnResponse($this->child . " was created", null, Status::SUCCESS, StatusCodes::CREATED);
        else
            return $this->returnResponse($this->child . " was not created", null, Status::ERROR, StatusCodes::INTERNAL_SERVER_ERROR);
    }

    public function fullyUpdate($pId, $cId, $obj) {
        //vars
        $arr = null;
        $data = null;

        //sanitize parent and child ids
        $pId = filter_var($pId, FILTER_SANITIZE_STRING);
        $cId = filter_var($cId, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($pId, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->parent . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);
        if(!filter_var($cId, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->child . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check if valid json
        if(!json_decode($obj))
            return $this->returnResponse("JSON format is incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);
        else
            $arr = json_decode($obj, true);

        //check if valid keys exist
        if(array_key_exists("CommentPost", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value))
                    return $this->returnResponse("No values can be empty", null, Status::ERROR, StatusCodes::BAD_REQUEST);
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create
            $data = new Comment();
            $data->GameId = $pId;
            $data->CommentPost = $arr['CommentPost'];
        } else
            return $this->returnResponse("Key names are incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check parent
        if(!$this->checkParent($pId))
            return $this->returnResponse($this->parent . "Id does not exist", null, Status::NO_RESULTS, StatusCodes::NOT_FOUND);

        //Prepare SQL
        $sth = $this->db->prepare("UPDATE " . $this->child .
            " Set CommentPost = :commentPost, GameId = :gameId 
                                    WHERE CommentId = :commentId"); //create statement
        $sth->bindParam(':commentPost', $data->CommentPost);
        $sth->bindParam(':gameId', $data->GameId);
        $sth->bindParam(':commentId', $cId);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount())
            return $this->returnResponse($this->child . " was fully updated", null, Status::SUCCESS, StatusCodes::CREATED);
        else
            return $this->returnResponse($this->child . " was not fully updated", null, Status::ERROR, StatusCodes::INTERNAL_SERVER_ERROR);
    }

    public function partiallyUpdate($pId, $cId, $obj) {
        //vars
        $arr = null;
        $data = null;

        //sanitize parent and child ids
        $pId = filter_var($pId, FILTER_SANITIZE_STRING);
        $cId = filter_var($cId, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($pId, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->parent . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);
        if(!filter_var($cId, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->child . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check if valid json
        if(!json_decode($obj))
            return $this->returnResponse("JSON format is incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);
        else
            $arr = json_decode($obj, true);

        //check if valid keys exist
        if(array_key_exists("CommentPost", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value))
                    return $this->returnResponse("No values can be empty", null, Status::ERROR, StatusCodes::BAD_REQUEST);
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create
            $data = new Comment();
            $data->GameId = $pId;
            $data->CommentPost = $arr['CommentPost'];
        } else
            return $this->returnResponse("Key names are incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check parent
        if(!$this->checkParent($pId))
            return $this->returnResponse($this->parent . "Id does not exist", null, Status::NO_RESULTS, StatusCodes::NOT_FOUND);

        //Prepare SQL
        $startStatement = "UPDATE " . $this->child . " SET ";
        $endStatement = " WHERE CommentId=:commentId";
        $set = array();
        if(isset($data->CommentPost))
            array_push($set, "CommentPost=:commentPost");
        $setString = implode(", ", $set);
        $statement = $startStatement . $setString . $endStatement;

        $sth = $this->db->prepare($statement); //create statement
        if(isset($data->CommentPost))
            $sth->bindParam(':commentPost', $data->CommentPost);
        $sth->bindParam(':commentId', $cId);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount())
            return $this->returnResponse($this->child . " was partially updated", null, Status::SUCCESS, StatusCodes::CREATED);
        else
            return $this->returnResponse($this->child . " was not partially updated", null, Status::ERROR, StatusCodes::INTERNAL_SERVER_ERROR);
    }

    public function delete($pId, $cId) {
        //sanitize parent and child ids
        $pId = filter_var($pId, FILTER_SANITIZE_STRING);
        $cId = filter_var($cId, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($pId, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->parent . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);
        if(!filter_var($cId, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->child . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check parent
        if(!$this->checkParent($pId))
            return $this->returnResponse($this->parent . "Id does not exist", null, Status::NO_RESULTS, StatusCodes::NOT_FOUND);

        //Prepare SQL
        $sth = $this->db->prepare("DELETE FROM " . $this->child . " 
                                   WHERE " . $this->child . "Id = :id"); //create statement
        $sth->bindParam(':id', $cId);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount())
            return $this->returnResponse($this->child . " was deleted", null, Status::SUCCESS, StatusCodes::CREATED);
        else
            return $this->returnResponse($this->child . " does not exist and was not deleted", null, Status::ERROR, StatusCodes::INTERNAL_SERVER_ERROR);
    }
}