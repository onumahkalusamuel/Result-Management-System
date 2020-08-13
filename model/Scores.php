<?php
namespace Model;

class Scores {
    // database table
    private $table = 'scores', 
    $examinationTable ='examination',
    $papersTable ='papers',
    $conn;
    
    private $DBTableSchema = 
        "CREATE TABLE IF NOT EXISTS 
        `scores`(
            `ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `studentID` int(11) NOT NULL,
            `paperID` int(11) NOT NULL,
            `examinationID` int(11) DEFAULT NULL,
            `score` float DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    
    // properties declaration
    public $ID, $studentID, $paperID, $examinationID, $score, $classCategoryID, $subjectID; //some are borrowed properties

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

        $query = "SELECT 
        s.*, 
        e.examinationTitle, 
        p.subjectID,
        p.classCategoryID
        FROM {$this->table} AS s 
        LEFT JOIN ({$this->examinationTable} AS e, {$this->papersTable} AS p) 
        ON s.examinationID = e.ID 
        AND s.paperID = p.ID 
        WHERE 1 " . 
        (!empty($this->ID) ? " AND s.ID={$this->ID}" : null) .
        (!empty($this->studentID) ? " AND s.studentID='{$this->studentID}'" : null) .
        (!empty($this->paperID) ? " AND s.paperID='{$this->paperID}'" : null) .
        (!empty($this->examinationID) ? " AND s.examinationID='{$this->examinationID}'" : null) .
        (!empty($this->subjectID) ? " AND p.subjectID='{$this->subjectID}'" : null) .
        (!empty($this->classCategoryID) ? " AND p.classCategoryID='{$this->classCategoryID}'" : null) .
        (!empty($this->score) ? " AND s.score='{$this->score}'" : null);
        $stmt = $db->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) $return[] = $row;

        return $return;

    }

    // create
    public function create() {

        if(empty($this->studentID)) return 0;

        global $db;

        //check existence
        $query = "SELECT ID FROM {$this->table} WHERE studentID='{$this->studentID}' AND paperID='{$this->paperID}' AND examinationID='{$this->examinationID}'";

        $stmt = $db->prepare($query);
        $stmt->execute();

        if($stmt->rowCount() > 0 ) {
            $this->ID = $stmt->fetch(\PDO::FETCH_ASSOC)['ID'];
            return $this->update();
        }

        //continue
        $query = "INSERT INTO {$this->table} SET " . 
        (!empty($this->studentID) ? " studentID='{$this->studentID}' " : null).
        (!empty($this->paperID) ? ", paperID='{$this->paperID}' " : null).
        (!empty($this->examinationID) ? ", examinationID='{$this->examinationID}' " : null).
        (!empty($this->score) ? ", score='{$this->score}' " : null);

        $stmt = $db->prepare($query);
        $stmt->execute();

        if($stmt->rowCount()==1) return 1;
        
        printf("Error: %s, \n", json_encode($stmt->errorInfo()));
        return 0;

    }
    // update
    public function update() {

        global $db;

        if(empty($this->ID) || empty($this->studentID)) return 0; 

        //continue
        $query = "UPDATE {$this->table} SET " . 
        (!empty($this->studentID) ? " studentID='{$this->studentID}' " : null).
        (!empty($this->paperID) ? ", paperID='{$this->paperID}' " : null).
        (!empty($this->examinationID) ? ", examinationID='{$this->examinationID}' " : null).
        (!empty($this->score) ? ", score='{$this->score}' " : null).
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