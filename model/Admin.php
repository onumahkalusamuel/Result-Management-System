<?php
namespace Model;

class Admin {
    // database table
    private $table = 'admin', $conn;
    private $DBTableSchema = 
    "CREATE TABLE IF NOT EXISTS 
    `admin`(
        `ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `lastName` varchar(50) NOT NULL,
        `firstName` varchar(50) NOT NULL,
        `middleName` varchar(50) DEFAULT NULL,
        `userName` varchar(50) NOT NULL,
        `password` varchar(256) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    
    // properties declaration
    public $ID, $lastName, $firstName, $middleName, $userName, $password;

    // create DB table
    public function createDBTable() {
        global $db;
        $this->dropDBTable();
        $stmt = $db->prepare($this->DBTableSchema);
        $stmt->execute();
    }

    // drop DB table
    private function dropDBTable() {
        global $db;
        $stmt = $db->prepare("DROP TABLE IF EXISTS {$this->table}");
        $stmt->execute();
    }

    // read
    public function read() {

        global $db;
        $return = [];

        $query = "SELECT ID, lastName, firstName, middleName, userName FROM {$this->table} " . (!empty($this->ID) ? " WHERE ID={$this->ID}" : null);
        $stmt = $db->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) $return[] = $row;

        return $return;

    }

    // read
    public function readLogin() {
        global $db;
        $query = "SELECT * FROM {$this->table} WHERE userName='{$this->userName}' LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // create
    public function create() {

        global $db;

        //check existence
        $query = "SELECT * FROM {$this->table} WHERE username='{$this->userName}'";
        $stmt = $db->prepare($query);
        $stmt->execute();

        if($stmt->rowCount()>0) return -1;

        //continue
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        $query = "INSERT INTO {$this->table} SET " . 
        (!empty($this->lastName) ? " lastName='{$this->lastName}' " : null).
        (!empty($this->firstName) ? ", firstName='{$this->firstName}' " : null).
        (!empty($this->middleName) ? ", middleName='{$this->middleName}' " : null).
        (!empty($this->userName) ? ", userName='{$this->userName}' " : null).
        (!empty($this->password) ? ", password='{$this->password}' " : null);

        $stmt = $db->prepare($query);
        $stmt->execute();

        if($stmt->rowCount()==1) return 1;
        
        printf("Error: %s, \n", json_encode($stmt->errorInfo()));
        return 0;

    }
    // update
    public function update() {

        global $db;

        //continue
        if(!empty($this->password)) $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        $query = "UPDATE {$this->table} SET " . 
        (!empty($this->lastName) ? " lastName='{$this->lastName}' " : null).
        (!empty($this->firstName) ? ", firstName='{$this->firstName}' " : null).
        (!empty($this->middleName) ? ", middleName='{$this->middleName}' " : null).
        (!empty($this->userName) ? ", userName='{$this->userName}' " : null).
        (!empty($this->password) ? ", password='{$this->password}' " : null).
        " WHERE ID={$this->ID} ";

        $stmt = $db->prepare($query);
        $stmt->execute();

        if($stmt->rowCount()==1 || empty((int)$stmt->errorInfo()[0])) return 1;
        
        printf("Error: %s, \n", json_encode($stmt->errorInfo()));
        return 0;

    }
    // delete
    public function delete() {
        
        global $db;

        if(empty($this->ID)) return 0;

        $query = "DELETE FROM {$this->table} WHERE ID={$this->ID} ";

        $stmt = $db->prepare($query);
        $stmt->execute();

        if($stmt->rowCount()==1) return 1;
        
        return 0;
    }
}