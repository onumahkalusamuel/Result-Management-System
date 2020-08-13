<?php
namespace Model;

class Students {
    // database table
    private $table = 'students', 
    $classCategoryTable = 'classcategory',
    $conn;

    private $DBTableSchema = 
        "CREATE TABLE IF NOT EXISTS 
        `students`(
            `ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `lastName` varchar(50) NOT NULL,
            `firstName` varchar(50) NOT NULL,
            `middleName` varchar(50) DEFAULT NULL,
            `admissionNumber` varchar(50) DEFAULT NULL,
            `examinationNumber` varchar(50) NOT NULL,
            `classCategoryID` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    
    // properties declaration
    public $ID, $lastName, $firstName, $middleName, $admissionNumber, $examinationNumber, $classCategoryID;

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

        $query = "SELECT s.*, cC.classCategoryTitle FROM {$this->table} AS s LEFT JOIN {$this->classCategoryTable} AS cC ON s.classCategoryID = cC.ID WHERE 1 " .
         (!empty($this->ID) ? " AND s.ID={$this->ID}" : null) . 
         (!empty($this->lastName) ? " AND s.lastName='{$this->lastName}'" : null) .
         (!empty($this->firstName) ? " AND s.firstName='{$this->firstName}'" : null) .
         (!empty($this->middleName) ? " AND s.middleName='{$this->middleName}'" : null) .
         (!empty($this->admissionNumber) ? " AND s.admissionNumber='{$this->admissionNumber}'" : null) .
         (!empty($this->examinationNumber) ? " AND s.examinationNumber='{$this->examinationNumber}'" : null) .
         (!empty($this->classCategoryID) ? " AND s.classCategoryID='{$this->classCategoryID}'" : null) . 
         " ORDER BY s.classCategoryID ASC, cC.classCategoryTitle ASC, s.lastName ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) $return[] = $row;

        return $return;

    }

    // create
    public function create() {

        global $db;

        //check existence
        $read = $this->read();        
        if(count($read) > 0) return -1;

        //continue
        $query = "INSERT INTO {$this->table} SET " . 
        (!empty($this->lastName) ? " lastName='{$this->lastName}' " : null).
        (!empty($this->firstName) ? ", firstName='{$this->firstName}' " : null).
        (!empty($this->middleName) ? ", middleName='{$this->middleName}' " : null).
        (!empty($this->admissionNumber) ? ", admissionNumber='{$this->admissionNumber}' " : null).
        (!empty($this->examinationNumber) ? ", examinationNumber='{$this->examinationNumber}' " : null) .
        (!empty($this->classCategoryID) ? ", classCategoryID='{$this->classCategoryID}' " : null);

        $stmt = $db->prepare($query);
        $stmt->execute();

        if($stmt->rowCount()==1) return 1;
        
        printf("Error: %s, \n", json_encode($stmt->errorInfo()));
        return 0;

    }
    // update
    public function update() {

        global $db;

        if(empty($this->ID) || empty($this->lastName)) return 0;

        $query = "UPDATE {$this->table} SET " . 
        (!empty($this->lastName) ? " lastName='{$this->lastName}' " : null).
        (!empty($this->firstName) ? ", firstName='{$this->firstName}' " : null).
        (!empty($this->middleName) ? ", middleName='{$this->middleName}' " : null).
        (!empty($this->admissionNumber) ? ", admissionNumber='{$this->admissionNumber}' " : null).
        (!empty($this->examinationNumber) ? ", examinationNumber='{$this->examinationNumber}' " : null).
        (!empty($this->classCategoryID) ? ", classCategoryID='{$this->classCategoryID}' " : null).
        " WHERE ID={$this->ID} ";

        $stmt = $db->prepare($query);
        $stmt->execute();
        
        if($stmt->rowCount()==1 || empty((int)$stmt->errorInfo()[0])) return 1;
        
        printf("Error: %s, \n", json_encode($stmt->errorInfo()));
        return 0;

    }
    // upgrade
    public function upgrade($classCategoryID = null, $students = "") {

        if(empty($classCategoryID) || empty($students)) return 0;

        global $db;

        $query = "UPDATE {$this->table} SET classCategoryID={$classCategoryID} WHERE ID IN ({$students})";

        $stmt = $db->prepare($query);
        $stmt->execute();
        
        if($stmt->rowCount() >= 1 || empty((int)$stmt->errorInfo()[0])) return 1;
        
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