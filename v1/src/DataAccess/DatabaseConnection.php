<?php
/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 */

namespace AdHocSportsGames\DataAccess;

use PDO;
use PDOException;

require_once 'DatabaseConfig.php';

/**
 * Class DatabaseConnection
 */
class DatabaseConnection implements DatabaseInterface {
    /* The database connection */
    private $instance;

    /**
     * Get the database connection
     * @return PDO
     */
    public function getInstance() {
        try {
            $connectionString = "mysql:host=" . HOST . ";dbname=" . DBNAME;
            $this->instance = new PDO($connectionString, USER, PASS);
            $this->instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            //$this->logger->addInfo($e->getMessage());
            die();
        }
        return $this->instance;
    }
}