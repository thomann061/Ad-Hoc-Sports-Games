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
use PDO;

class GameSQL implements SQLInterface {
    private $db;
    private $name;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db->getInstance();
        $this->name = "Game";
    }

    public function selectAll() {
        //Prepare SQL
        $sth = $this->db->prepare("SELECT * FROM " . $this->name); //create statement
        $sth->execute();  //execute the statement on the database

        //fetch directly into class
        $data = $sth->fetchAll(PDO::FETCH_CLASS, "AdHocSportsGames\\Model\\" . $this->name);

        //the wrapper
        $wrapper = new JsonWrapper();

        if($sth->rowCount()) {
            $wrapper->setMessage($this->name . "s were retrieved");
            $wrapper->setData($data);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::OK);
        } else {
            $wrapper->setMessage("No " . $this->name . "s exist");
            $wrapper->setData(null);
            $wrapper->setStatus("No results");
            $wrapper->setCode(StatusCodes::NOT_FOUND);
        }
        return $wrapper;
    }

    public function select($id) {
        //the wrapper
        $wrapper = new JsonWrapper();

        //sanitize id
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($id, FILTER_VALIDATE_INT) ){
            $wrapper->setMessage($this->name . " Id is not a number");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //Prepare SQL
        $sth = $this->db->prepare("SELECT * FROM " . $this->name .
                                    " WHERE " . $this->name . "Id = :id"); //create statement
        $sth->bindParam(':id', $id);
        $sth->execute();  //execute the statement on the database

        //fetch directly into class
        $data = $sth->fetchAll(PDO::FETCH_CLASS, "AdHocSportsGames\\Model\\" . $this->name);

        if($sth->rowCount()) {
            $wrapper->setMessage($this->name . " was retrieved");
            $wrapper->setData($data);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::OK);
        } else {
            $wrapper->setMessage($this->name . " does not exist");
            $wrapper->setData(null);
            $wrapper->setStatus("No results");
            $wrapper->setCode(StatusCodes::NOT_FOUND);
        }
        return $wrapper;
    }

    public function insert($obj) {
        //vars
        $wrapper = new JsonWrapper();
        $arr = null;
        $data = null;

        //check if valid json
        if(!json_decode($obj)) {
            $wrapper->setMessage("JSON format is incorrect");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        } else
            $arr = json_decode($obj, true);

        //check if valid keys exist
        if(array_key_exists("GameName", $arr) && array_key_exists("GameType", $arr)
            && array_key_exists("GameLocation", $arr) && array_key_exists("GameDateTime", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value)) {
                    $wrapper->setMessage("No values can be empty");
                    $wrapper->setData(null);
                    $wrapper->setStatus("Error");
                    $wrapper->setCode(StatusCodes::BAD_REQUEST);
                    return $wrapper;
                }
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $key[$value] = filter_var($value, FILTER_SANITIZE_STRING);
            //create
            $data = new Game();
            $data->GameName = $arr['GameName'];
            $data->GameType = $arr['GameType'];
            $data->GameLocation = $arr['GameLocation'];
            $data->GameDateTime = $arr['GameDateTime'];
        } else {
            $wrapper->setMessage("Key names are incorrect");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //Prepare SQL
        $sth = $this->db->prepare("INSERT INTO " . $this->name . " (GameName, GameType, 
                                    GameLocation, GameDateTime) 
                                    VALUES(:gameName, :gameType, :gameLocation,
                                    :gameDateTime)"); //create statement
        $sth->bindParam(':gameName', $data->GameName);
        $sth->bindParam(':gameType', $data->GameType);
        $sth->bindParam(':gameLocation', $data->GameLocation);
        $sth->bindParam(':gameDateTime', $data->GameDateTime);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount()) {
            $wrapper->setMessage($this->name . " was created");
            $wrapper->setData(null);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::CREATED);
        } else {
            $wrapper->setMessage($this->name . " was not created");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::INTERNAL_SERVER_ERROR);
        }
        return $wrapper;
    }

    public function fullyUpdate($obj, $id) {
        //vars
        $wrapper = new JsonWrapper();
        $arr = null;
        $data = null;

        //sanitize id
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($id, FILTER_VALIDATE_INT) ){
            $wrapper->setMessage($this->name . " Id is not a number");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //check if valid json
        if(!json_decode($obj)) {
            $wrapper->setMessage("JSON format is incorrect");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        } else
            $arr = json_decode($obj, true);

        //check if valid keys exist
        if(array_key_exists("GameName", $arr) && array_key_exists("GameType", $arr)
            && array_key_exists("GameLocation", $arr) && array_key_exists("GameDateTime", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value)) {
                    $wrapper->setMessage("No values can be empty");
                    $wrapper->setData(null);
                    $wrapper->setStatus("Error");
                    $wrapper->setCode(StatusCodes::BAD_REQUEST);
                    return $wrapper;
                }
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $key[$value] = filter_var($value, FILTER_SANITIZE_STRING);
            //create
            $data = new Game();
            $data->GameName = $arr['GameName'];
            $data->GameType = $arr['GameType'];
            $data->GameLocation = $arr['GameLocation'];
            $data->GameDateTime = $arr['GameDateTime'];
        } else {
            $wrapper->setMessage("Key names are incorrect");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //Prepare SQL
        $sth = $this->db->prepare("UPDATE " . $this->name .
                                    " Set GameName = :gameName, GameType = :gameType, 
                                    GameLocation = :gameLocation, GameDateTime = :gameDateTime 
                                    WHERE GameId = :gameId"); //create statement
        $sth->bindParam(':gameName', $data->GameName);
        $sth->bindParam(':gameType', $data->GameType);
        $sth->bindParam(':gameLocation', $data->GameLocation);
        $sth->bindParam(':gameDateTime', $data->GameDateTime);
        $sth->bindParam(':gameId', $id);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount()) {
            $wrapper->setMessage($this->name . " was fully updated");
            $wrapper->setData(null);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::OK);
        } else {
            $wrapper->setMessage($this->name . " was not fully updated");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::INTERNAL_SERVER_ERROR);
        }
        return $wrapper;
    }

    public function partiallyUpdate($obj, $id) {
        //vars
        $wrapper = new JsonWrapper();
        $arr = null;
        $data = null;

        //sanitize id
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($id, FILTER_VALIDATE_INT) ){
            $wrapper->setMessage($this->name . " Id is not a number");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //check if valid json
        if(!json_decode($obj)) {
            $wrapper->setMessage("JSON format is incorrect");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        } else
            $arr = json_decode($obj, true);

        //check if valid keys exist
        if(array_key_exists("GameName", $arr) || array_key_exists("GameType", $arr)
            || array_key_exists("GameLocation", $arr) || array_key_exists("GameDateTime", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value)) {
                    $wrapper->setMessage("No values can be empty");
                    $wrapper->setData(null);
                    $wrapper->setStatus("Error");
                    $wrapper->setCode(StatusCodes::BAD_REQUEST);
                    return $wrapper;
                }
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $key[$value] = filter_var($value, FILTER_SANITIZE_STRING);
            //create
            $data = new Game();
            if(isset($arr['GameName']))
                $data->GameName = $arr['GameName'];
            if(isset($arr['GameType']))
                $data->GameType = $arr['GameType'];
            if(isset($arr['GameLocation']))
                $data->GameLocation = $arr['GameLocation'];
            if(isset($arr['GameDateTime']))
                $data->GameDateTime = $arr['GameDateTime'];
        } else {
            $wrapper->setMessage("Key names are incorrect");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //Prepare SQL
        $startStatement = "UPDATE " . $this->name . " SET ";
        $endStatement = " WHERE GameId=:gameId";
        $set = array();
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

        if($sth->rowCount()) {
            $wrapper->setMessage($this->name . " was partially updated");
            $wrapper->setData(null);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::OK);
        } else {
            $wrapper->setMessage($this->name . " was not partially updated");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::INTERNAL_SERVER_ERROR);
        }
        return $wrapper;
    }

    public function delete($id) {
        //the wrapper
        $wrapper = new JsonWrapper();

        //sanitize id
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($id, FILTER_VALIDATE_INT) ){
            $wrapper->setMessage($this->name . " Id is not a number");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //Prepare SQL
        $sth = $this->db->prepare("DELETE FROM " . $this->name . " 
                                   WHERE " . $this->name . "Id = :id"); //create statement
        $sth->bindParam(':id', $id);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount()) {
            $wrapper->setMessage($this->name . " was deleted");
            $wrapper->setData(null);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::OK);
        } else {
            $wrapper->setMessage($this->name . " does not exist and was not deleted");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::NOT_FOUND);
        }
        return $wrapper;
    }
}