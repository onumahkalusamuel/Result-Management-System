<?php
namespace Model;

class ClassCategory {
    // database table
    private $table = 'classCategory', 
    $gradingTable = 'grading', 
    $conn;
    private $DBTableSchema = 
        "CREATE TABLE IF NOT EXISTS 
        `classCategory`(
            `ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `classCategoryTitle` varchar(100) NOT NULL,
            `classCategorySlug` varchar(100) NOT NULL,
            `gradingID` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        
    // properties declaration
    public $ID, $classCategoryTitle, $classCategorySlug, $gradingID;

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

        $query = "SELECT cC.*, g.gradingTitle FROM {$this->table} AS cC 
        LEFT JOIN {$this->gradingTable} AS g 
        ON cC.gradingID = g.ID WHERE 1 " . 
        (!empty($this->ID) ? " AND cC.ID={$this->ID}" : null) .
        (!empty($this->classCategoryTitle) ? " AND cC.classCategoryTitle='{$this->classCategoryTitle}'" : null) .
        (!empty($this->classCategorySlug) ? " AND cC.classCategorySlug='{$this->classCategorySlug}'" : null) . 
        " ORDER BY cC.classCategoryTitle ASC ";
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
        $this->classCategorySlug = \Core\Functions::make_slug($this->classCategoryTitle);

        $query = "INSERT INTO {$this->table} SET " . 
        (!empty($this->classCategoryTitle) ? " classCategoryTitle='{$this->classCategoryTitle}' " : null) .
        (!empty($this->classCategorySlug) ? ", classCategorySlug='{$this->classCategorySlug}' " : null) .
        (!empty($this->gradingID) ? ", gradingID={$this->gradingID} " : null);

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
        $this->classCategorySlug = \Core\Functions::make_slug($this->classCategoryTitle);

        $query = "UPDATE {$this->table} SET " . 
        (!empty($this->classCategoryTitle) ? " classCategoryTitle='{$this->classCategoryTitle}' " : null) .
        (!empty($this->classCategorySlug) ? ", classCategorySlug='{$this->classCategorySlug}' " : null) .
        (!empty($this->gradingID) ? ", gradingID={$this->gradingID} " : null) .
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