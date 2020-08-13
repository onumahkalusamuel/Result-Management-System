<?php
namespace Model;

class GradingSystem {
    // database table
    private $table = 'gradingSystem',
    $gradingTable = 'grading',
    $conn;

    private $DBTableSchema = 
        "CREATE TABLE IF NOT EXISTS 
        `gradingSystem`(
            `ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `gradingID` int(11) NOT NULL,
            `ordering` int(11) NOT NULL,
            `minimumScore` int(11) DEFAULT 0,
            `maximumScore` int(11) DEFAULT 0,
            `grade` varchar(5) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    
    // properties declaration
    public $ID, $gradingID, $ordering, $minimumScore, $maximumScore, $grade = '';

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

        $query = "SELECT gS.*, g.gradingTitle FROM {$this->table} AS gS LEFT JOIN {$this->gradingTable} AS g ON gS.gradingID = g.ID WHERE 1 " .
         (!empty($this->ID) ? " AND gS.ID={$this->ID} " : null) .
         (!empty($this->gradingID) ? " AND gS.gradingID={$this->gradingID} " : null) .
         (!empty($this->minimumScore) ? " AND gS.minimumScore={$this->minimumScore} " : null) .
         (!empty($this->maximumScore) ? " AND gS.maximumScore={$this->maximumScore} " : null) .
         (!empty($this->grade) ? " AND gS.grade='{$this->grade}' " : null) .
         (!empty($this->ordering) ? " AND gS.ordering={$this->ordering} " : null) .
         " ORDER BY gS.gradingID DESC, gS.ordering DESC ";
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

        if(count( $read ) > 0) return -1;

        //continue
        $query = "INSERT INTO {$this->table} SET " . 
        (!empty($this->gradingID) ? " gradingID='{$this->gradingID}' " : null).
        (!empty($this->ordering) ? ", ordering='{$this->ordering}' " : null).
        (!empty($this->minimumScore) ? ", minimumScore='{$this->minimumScore}' " : null).
        (!empty($this->maximumScore) ? ", maximumScore='{$this->maximumScore}' " : null).
        (!empty($this->grade) ? ", grade='{$this->grade}' " : null);

        $stmt = $db->prepare($query);
        $stmt->execute();

        if($stmt->rowCount()==1) return 1;
        
        printf("Error: %s, \n", json_encode($stmt->errorInfo()));
        return 0;

    }
    // update
    public function update() {

        global $db;
        
        if(empty($this->ID)) return 0;

        //continue
        $query = "UPDATE {$this->table} SET " . 
        (!empty($this->gradingID) ? " gradingID='{$this->gradingID}' " : null).
        (!empty($this->ordering) ? ", ordering='{$this->ordering}' " : null).
        (!empty($this->minimumScore) ? ", minimumScore='{$this->minimumScore}' " : null).
        (!empty($this->maximumScore) ? ", maximumScore='{$this->maximumScore}' " : null).
        (!empty($this->grade) ? ", grade='{$this->grade}' " : null).
        " WHERE ID={$this->ID} ";

        $stmt = $db->prepare($query);
        $stmt->execute();
        
        if($stmt->rowCount()==1) return 1;
        
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