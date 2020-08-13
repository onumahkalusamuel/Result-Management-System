<?php

namespace Core;

use \Core\Config as Config;

class Database {

    public $conn;

    public function __construct() {
    }

    public function connect(){

        $this->conn = null;

        try {
            $this->conn = new \PDO("mysql:host=" . Config::HOST . ";dbname=" . Config::DBNAME, Config::USERNAME, Config::PASSWORD);

        } catch(\PDOException $exception) {

            return "Connection error: {$exception->getMessage()}";

        }

        return $this->conn;
        
    }
}