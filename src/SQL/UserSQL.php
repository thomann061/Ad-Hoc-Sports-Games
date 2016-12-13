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
use PDO;

class UserSQL implements SQLParentInterface {
    private $db;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db->getInstance();
    }

    public function selectAll() {
        //Prepare SQL
        $sth = $this->db->prepare("SELECT * FROM User"); //create statement
        $sth->execute();  //execute the statement on the database

        //fetch directly into class
        $users = $sth->fetchAll(PDO::FETCH_CLASS, 'AdHocSportsGames\Model\User');

        //the wrapper
        $wrapper = new JsonWrapper();

        if($sth->rowCount()) {
            $wrapper->setMessage("Users were retrieved");
            $wrapper->setData($users);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::OK);
        } else {
            $wrapper->setMessage("No users exist");
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
            $wrapper->setMessage("User Id is not a number");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //Prepare SQL
        $sth = $this->db->prepare("SELECT * FROM User
                                   WHERE UserId = :id"); //create statement
        $sth->bindParam(':id', $id);
        $sth->execute();  //execute the statement on the database

        //fetch directly into class
        $user = $sth->fetchAll(PDO::FETCH_CLASS, 'AdHocSportsGames\Model\User');

        if($sth->rowCount()) {
            $wrapper->setMessage("User was retrieved");
            $wrapper->setData($user);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::OK);
        } else {
            $wrapper->setMessage("User does not exist");
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
        $user = null;

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
        if(array_key_exists("UserName", $arr) && array_key_exists("UserPassword", $arr)
            && array_key_exists("UserFirstName", $arr) && array_key_exists("UserLastName", $arr)) {
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
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create a user
            $user = new User();
            $user->UserName = $arr['UserName'];
            $user->UserPassword = $arr['UserPassword'];
            $user->UserFirstName = $arr['UserFirstName'];
            $user->UserLastName = $arr['UserLastName'];
        } else {
            $wrapper->setMessage("Key names are incorrect");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //Prepare SQL
        $sth = $this->db->prepare("INSERT INTO User (UserName, UserPassword, 
                                    UserFirstName, UserLastName) 
                                    VALUES(:userName, :userPassword, :userFirstName,
                                    :userLastName)"); //create statement
        $sth->bindParam(':userName', $user->UserName);
        $sth->bindParam(':userPassword', $user->UserPassword);
        $sth->bindParam(':userFirstName', $user->UserFirstName);
        $sth->bindParam(':userLastName', $user->UserLastName);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount()) {
            $wrapper->setMessage("User was created");
            $wrapper->setData(null);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::CREATED);
        } else {
            $wrapper->setMessage("User was not created");
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
        $user = null;

        //sanitize id
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($id, FILTER_VALIDATE_INT) ){
            $wrapper->setMessage("User Id is not a number");
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
        if(array_key_exists("UserName", $arr) && array_key_exists("UserPassword", $arr)
            && array_key_exists("UserFirstName", $arr) && array_key_exists("UserLastName", $arr)) {
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
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create a user
            $user = new User();
            $user->UserName = $arr['UserName'];
            $user->UserPassword = $arr['UserPassword'];
            $user->UserFirstName = $arr['UserFirstName'];
            $user->UserLastName = $arr['UserLastName'];
        } else {
            $wrapper->setMessage("Key names are incorrect");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //Prepare SQL
        $sth = $this->db->prepare("UPDATE User 
                                    Set UserName = :userName, UserPassword = :userPassword, 
                                    UserFirstName = :userFirstName, UserLastName = :userLastName 
                                    WHERE UserId = :userId"); //create statement
        $sth->bindParam(':userName', $user->UserName);
        $sth->bindParam(':userPassword', $user->UserPassword);
        $sth->bindParam(':userFirstName', $user->UserFirstName);
        $sth->bindParam(':userLastName', $user->UserLastName);
        $sth->bindParam(':userId', $id);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount()) {
            $wrapper->setMessage("User was fully updated");
            $wrapper->setData(null);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::OK);
        } else {
            $wrapper->setMessage("User was not fully updated");
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
        $user = null;

        //sanitize id
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        //type check id
        if(!filter_var($id, FILTER_VALIDATE_INT) ){
            $wrapper->setMessage("User Id is not a number");
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
        if(array_key_exists("UserName", $arr) || array_key_exists("UserPassword", $arr)
            || array_key_exists("UserFirstName", $arr) || array_key_exists("UserLastName", $arr)) {
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
                $arr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            //create a user
            $user = new User();
            if(isset($arr['UserName']))
                $user->UserName = $arr['UserName'];
            if(isset($arr['UserPassword']))
                $user->UserPassword = $arr['UserPassword'];
            if(isset($arr['UserFirstName']))
                $user->UserFirstName = $arr['UserFirstName'];
            if(isset($arr['UserLastName']))
                $user->UserLastName = $arr['UserLastName'];
        } else {
            $wrapper->setMessage("Key names are incorrect");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //Prepare SQL
        $startStatement = "UPDATE User SET ";
        $endStatement = " WHERE UserId=:userId";
        $set = array();
        if(isset($user->UserName))
            array_push($set, "UserName=:userName");
        if(isset($user->UserPassword))
            array_push($set, "UserPassword=:userPassword");
        if(isset($user->UserFirstName))
            array_push($set, "UserFirstName=:userFirstName");
        if(isset($user->UserLastName))
            array_push($set, "UserLastName=:userLastName");
        $setString = implode(", ", $set);
        $statement = $startStatement . $setString . $endStatement;

        $sth = $this->db->prepare($statement); //create statement
        if(isset($user->UserName))
            $sth->bindParam(':userName', $user->UserName);
        if(isset($user->UserPassword))
            $sth->bindParam(':userPassword', $user->UserPassword);
        if(isset($user->UserFirstName))
            $sth->bindParam(':userFirstName', $user->UserFirstName);
        if(isset($user->UserLastName))
            $sth->bindParam(':userLastName', $user->UserLastName);
        $sth->bindParam(':userId', $id);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount()) {
            $wrapper->setMessage("User was partially updated");
            $wrapper->setData(null);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::OK);
        } else {
            $wrapper->setMessage("User was not partially updated");
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
            $wrapper->setMessage("User Id is not a number");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::BAD_REQUEST);
            return $wrapper;
        }

        //Prepare SQL
        $sth = $this->db->prepare("DELETE FROM User
                                   WHERE UserId = :id"); //create statement
        $sth->bindParam(':id', $id);
        $sth->execute();  //execute the statement on the database

        if($sth->rowCount()) {
            $wrapper->setMessage("User was deleted");
            $wrapper->setData(null);
            $wrapper->setStatus("OK");
            $wrapper->setCode(StatusCodes::OK);
        } else {
            $wrapper->setMessage("User does not exist and was not deleted");
            $wrapper->setData(null);
            $wrapper->setStatus("Error");
            $wrapper->setCode(StatusCodes::NOT_FOUND);
        }
        return $wrapper;
    }
}