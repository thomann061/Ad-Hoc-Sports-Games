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
use AdHocSportsGames\Model\User;
use AdHocSportsGames\Utilities\JsonWrapper;
use AdHocSportsGames\Utilities\Status;
use PDO;

class UserSQL implements SQLParentInterface, JsonResponseInterface {
    private $db;
    private $name;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db->getInstance();
        $this->name = "User";
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
        $data = $sth->fetchAll(PDO::FETCH_CLASS, 'AdHocSportsGames\Model\User');

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
        if(array_key_exists("UserName", $arr) && array_key_exists("UserPassword", $arr)
            && array_key_exists("UserFirstName", $arr) && array_key_exists("UserLastName", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value))
                    return $this->returnResponse("No values can be empty", null, Status::ERROR, StatusCodes::BAD_REQUEST);
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create a user
            $data = new User();
            $data->UserName = $arr['UserName'];
            $data->UserPassword = $arr['UserPassword'];
            $data->UserFirstName = $arr['UserFirstName'];
            $data->UserLastName = $arr['UserLastName'];
        } else
            return $this->returnResponse("Key names are incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //Prepare SQL
        $sth = $this->db->prepare("INSERT INTO " . $this->name . " (UserName, UserPassword, 
                                    UserFirstName, UserLastName) 
                                    VALUES(:userName, :userPassword, :userFirstName,
                                    :userLastName)"); //create statement
        $sth->bindParam(':userName', $data->UserName);
        $sth->bindParam(':userPassword', $data->UserPassword);
        $sth->bindParam(':userFirstName', $data->UserFirstName);
        $sth->bindParam(':userLastName', $data->UserLastName);
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
        if(array_key_exists("UserName", $arr) && array_key_exists("UserPassword", $arr)
            && array_key_exists("UserFirstName", $arr) && array_key_exists("UserLastName", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value))
                    return $this->returnResponse("No values can be empty", null, Status::ERROR, StatusCodes::BAD_REQUEST);
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create a user
            $data = new User();
            $data->UserName = $arr['UserName'];
            $data->UserPassword = $arr['UserPassword'];
            $data->UserFirstName = $arr['UserFirstName'];
            $data->UserLastName = $arr['UserLastName'];
        } else
            return $this->returnResponse("Key names are incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //Prepare SQL
        $sth = $this->db->prepare("UPDATE " . $this->name .
                                    " Set UserName = :userName, UserPassword = :userPassword, 
                                    UserFirstName = :userFirstName, UserLastName = :userLastName 
                                    WHERE UserId = :userId"); //create statement
        $sth->bindParam(':userName', $data->UserName);
        $sth->bindParam(':userPassword', $data->UserPassword);
        $sth->bindParam(':userFirstName', $data->UserFirstName);
        $sth->bindParam(':userLastName', $data->UserLastName);
        $sth->bindParam(':userId', $id);
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
        if(array_key_exists("UserName", $arr) || array_key_exists("UserPassword", $arr)
            || array_key_exists("UserFirstName", $arr) || array_key_exists("UserLastName", $arr)) {
            //check for empty fields
            foreach($arr as $key => $value) {
                if(empty($value))
                    return $this->returnResponse("No values can be empty", null, Status::ERROR, StatusCodes::BAD_REQUEST);
            }
            //sanitize all values
            foreach($arr as $key => $value)
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create a user
            $data = new User();
            if(isset($arr['UserName']))
                $data->UserName = $arr['UserName'];
            if(isset($arr['UserPassword']))
                $data->UserPassword = $arr['UserPassword'];
            if(isset($arr['UserFirstName']))
                $data->UserFirstName = $arr['UserFirstName'];
            if(isset($arr['UserLastName']))
                $data->UserLastName = $arr['UserLastName'];
        } else
            return $this->returnResponse("Key names are incorrect", null, Status::ERROR, StatusCodes::BAD_REQUEST);

        //Prepare SQL
        $startStatement = "UPDATE " . $this->name . " SET ";
        $endStatement = " WHERE UserId=:userId";
        $set = array();
        if(isset($data->UserName))
            array_push($set, "UserName=:userName");
        if(isset($data->UserPassword))
            array_push($set, "UserPassword=:userPassword");
        if(isset($data->UserFirstName))
            array_push($set, "UserFirstName=:userFirstName");
        if(isset($data->UserLastName))
            array_push($set, "UserLastName=:userLastName");
        $setString = implode(", ", $set);
        $statement = $startStatement . $setString . $endStatement;

        $sth = $this->db->prepare($statement); //create statement
        if(isset($data->UserName))
            $sth->bindParam(':userName', $user->UserName);
        if(isset($data->UserPassword))
            $sth->bindParam(':userPassword', $user->UserPassword);
        if(isset($data->UserFirstName))
            $sth->bindParam(':userFirstName', $user->UserFirstName);
        if(isset($data->UserLastName))
            $sth->bindParam(':userLastName', $user->UserLastName);
        $sth->bindParam(':userId', $id);
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