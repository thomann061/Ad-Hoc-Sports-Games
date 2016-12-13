<?php
/**
 * Created by PhpStorm.
 * User: jthomann
 * Date: 12/7/16
 * Time: 8:05 AM
 */

namespace AdHocSportsGames\SQL;

use AdHocSportsGames\DataAccess\DatabaseInterface;
use AdHocSportsGames\Http\StatusCodes;
use AdHocSportsGames\Model\Game;
use AdHocSportsGames\Utilities\JsonWrapper;
use AdHocSportsGames\Utilities\Status;
use PDO;

class GameSQL implements SQLParentInterface, JsonResponseInterface {
    private $db;
    private $name;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db->getInstance();
        $this->name = "Game";
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

    public function selectAll() {
        //Prepare SQL
        $sth = $this->db->prepare("SELECT * FROM " . $this->name); //create statement
        $sth->execute();  //execute the statement on the database

        //fetch directly into class
        $data = $sth->fetchAll(PDO::FETCH_CLASS, "AdHocSportsGames\\Model\\" . $this->name);

        if($sth->rowCount())
            return $this->returnResponse($this->name . "s were retrieved", $data, Status::SUCCESS, StatusCodes::OK);
        else
            return $this->returnResponse("No " . $this->name . "s exist", null, Status::NO_RESULTS, StatusCodes::NOT_FOUND);
    }

    public function select($id) {
        //sanitize id
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($id, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->name . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //Prepare SQL
        $sth = $this->db->prepare("SELECT * FROM " . $this->name .
                                    " WHERE " . $this->name . "Id = :id"); //create statement
        $sth->bindParam(':id', $id);
        $sth->execute();  //execute the statement on the database

        //fetch directly into class
        $data = $sth->fetchAll(PDO::FETCH_CLASS, "AdHocSportsGames\\Model\\" . $this->name);

        if($sth->rowCount())
            return $this->returnResponse($this->name . " was retrieved", $data, Status::SUCCESS, StatusCodes::OK);
        else
            return $this->returnResponse($this->name . " does not exist", null, Status::NO_RESULTS, StatusCodes::NOT_FOUND);
    }

    public function insert($obj) {
        //vars
        $arr = null;
        $data = null;

        //check if valid json
        if(!json_decode($obj))
            $this->returnResponse("JSON format is incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);
        else
            $arr = json_decode($obj, true);

        //check if valid keys exist
        if(array_key_exists("UserId", $arr) && array_key_exists("GameName", $arr) && array_key_exists("GameType", $arr)
            && array_key_exists("GameLocation", $arr) && array_key_exists("GameDateTime", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value))
                    return $this->returnResponse("No values can be empty", null, Status::ERROR, StatusCodes::BAD_REQUEST);
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create
            $data = new Game();
            $data->UserId = $arr['UserId'];
            $data->GameName = $arr['GameName'];
            $data->GameType = $arr['GameType'];
            $data->GameLocation = $arr['GameLocation'];
            $data->GameDateTime = $arr['GameDateTime'];
        } else
            return $this->returnResponse("Key names are incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //Prepare SQL
        $sth = $this->db->prepare("INSERT INTO " . $this->name . " (UserId, GameName, GameType, 
                                    GameLocation, GameDateTime) 
                                    VALUES(:userId, :gameName, :gameType, :gameLocation,
                                    :gameDateTime)"); //create statement
        $sth->bindParam(':userId', $data->UserId);
        $sth->bindParam(':gameName', $data->GameName);
        $sth->bindParam(':gameType', $data->GameType);
        $sth->bindParam(':gameLocation', $data->GameLocation);
        $sth->bindParam(':gameDateTime', $data->GameDateTime);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount())
            return $this->returnResponse($this->name . " was created", null, Status::SUCCESS, StatusCodes::CREATED);
        else
            return $this->returnResponse($this->name . " was not created", null, Status::ERROR, StatusCodes::INTERNAL_SERVER_ERROR);
    }

    public function fullyUpdate($obj, $id) {
        //vars
        $wrapper = new JsonWrapper();
        $arr = null;
        $data = null;

        //sanitize id
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($id, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->name . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check if valid json
        if(!json_decode($obj))
            $this->returnResponse("JSON format is incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);
        else
            $arr = json_decode($obj, true);

        //check if valid keys exist
        if(array_key_exists("UserId", $arr) && array_key_exists("GameName", $arr) && array_key_exists("GameType", $arr)
            && array_key_exists("GameLocation", $arr) && array_key_exists("GameDateTime", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value))
                    return $this->returnResponse("No values can be empty", null, Status::ERROR, StatusCodes::BAD_REQUEST);
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create
            $data = new Game();
            $data->UserId = $arr['UserId'];
            $data->GameName = $arr['GameName'];
            $data->GameType = $arr['GameType'];
            $data->GameLocation = $arr['GameLocation'];
            $data->GameDateTime = $arr['GameDateTime'];
        } else
            return $this->returnResponse("Key names are incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //Prepare SQL
        $sth = $this->db->prepare("UPDATE " . $this->name .
                                    " Set UserId = :userId, GameName = :gameName, GameType = :gameType, 
                                    GameLocation = :gameLocation, GameDateTime = :gameDateTime 
                                    WHERE GameId = :gameId"); //create statement
        $sth->bindParam(':userId', $data->UserId);
        $sth->bindParam(':gameName', $data->GameName);
        $sth->bindParam(':gameType', $data->GameType);
        $sth->bindParam(':gameLocation', $data->GameLocation);
        $sth->bindParam(':gameDateTime', $data->GameDateTime);
        $sth->bindParam(':gameId', $id);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount())
            return $this->returnResponse($this->name . " was fully updated", null, Status::SUCCESS, StatusCodes::CREATED);
        else
            return $this->returnResponse($this->name . " was not fully updated", null, Status::ERROR, StatusCodes::INTERNAL_SERVER_ERROR);
    }

    public function partiallyUpdate($obj, $id) {
        //vars
        $arr = null;
        $data = null;

        //sanitize id
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($id, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->name . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //check if valid json
        if(!json_decode($obj))
            $this->returnResponse("JSON format is incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);
        else
            $arr = json_decode($obj, true);

        //check if valid keys exist
        if(array_key_exists("UserId", $arr) || array_key_exists("GameName", $arr) || array_key_exists("GameType", $arr)
            || array_key_exists("GameLocation", $arr) || array_key_exists("GameDateTime", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value))
                    return $this->returnResponse("No values can be empty", null, Status::ERROR, StatusCodes::BAD_REQUEST);
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create
            $data = new Game();
            if(isset($arr['UserId']))
                $data->UserId = $arr['UserId'];
            if(isset($arr['GameName']))
                $data->GameName = $arr['GameName'];
            if(isset($arr['GameType']))
                $data->GameType = $arr['GameType'];
            if(isset($arr['GameLocation']))
                $data->GameLocation = $arr['GameLocation'];
            if(isset($arr['GameDateTime']))
                $data->GameDateTime = $arr['GameDateTime'];
        } else
            return $this->returnResponse("Key names are incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //Prepare SQL
        $startStatement = "UPDATE " . $this->name . " SET ";
        $endStatement = " WHERE GameId=:gameId";
        $set = array();
        if(isset($data->UserId))
            array_push($set, "UserId=:userId");
        if(isset($data->GameName))
            array_push($set, "GameName=:gameName");
        if(isset($data->GameType))
            array_push($set, "GameType=:gameType");
        if(isset($data->GameLocation))
            array_push($set, "GameLocation=:gameLocation");
        if(isset($data->GameDateTime))
            array_push($set, "GameDateTime=:gameDateTime");
        $setString = implode(", ", $set);
        $statement = $startStatement . $setString . $endStatement;

        $sth = $this->db->prepare($statement); //create statement
        if(isset($data->UserId))
            $sth->bindParam(':userId', $data->UserId);
        if(isset($data->GameName))
            $sth->bindParam(':gameName', $data->GameName);
        if(isset($data->GameType))
            $sth->bindParam(':gameType', $data->GameType);
        if(isset($data->GameLocation))
            $sth->bindParam(':gameLocation', $data->GameLocation);
        if(isset($data->GameDateTime))
            $sth->bindParam(':gameDateTime', $data->GameDateTime);
        $sth->bindParam(':gameId', $id);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount())
            return $this->returnResponse($this->name . " was partially updated", null, Status::SUCCESS, StatusCodes::CREATED);
        else
            return $this->returnResponse($this->name . " was not partially updated", null, Status::ERROR, StatusCodes::INTERNAL_SERVER_ERROR);
    }

    public function delete($id) {
        //sanitize id
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($id, FILTER_VALIDATE_INT))
            return $this->returnResponse($this->name . " Id is not a number", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //Prepare SQL
        $sth = $this->db->prepare("DELETE FROM " . $this->name . " 
                                   WHERE " . $this->name . "Id = :id"); //create statement
        $sth->bindParam(':id', $id);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount())
            return $this->returnResponse($this->name . " was deleted", null, Status::SUCCESS, StatusCodes::CREATED);
        else
            return $this->returnResponse($this->name . " was not deleted", null, Status::ERROR, StatusCodes::INTERNAL_SERVER_ERROR);
    }
}