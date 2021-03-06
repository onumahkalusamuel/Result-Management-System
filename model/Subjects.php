<?php
namespace Model;

class Subjects {
    // database table
    private $table = 'subjects', $conn;
    private $DBTableSchema = 
        "CREATE TABLE IF NOT EXISTS 
        `subjects`(
            `ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `subjectTitle` varchar(100) NOT NULL,
            `subjectSlug` varchar(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    
    // properties declaration
    public $ID, $subjectTitle, $subjectSlug ;

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

        $query = "SELECT * FROM {$this->table} WHERE 1 " . 
        (!empty($this->ID) ? " AND ID={$this->ID}" : null) .
        (!empty($this->subjectTitle) ? " AND subjectTitle='{$this->subjectTitle}'" : null) .
        (!empty($this->subjectSlug) ? " AND subjectSlug='{$this->subjectSlug}'" : null);
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
        $this->subjectSlug = \Core\Functions::make_slug($this->subjectTitle);

        $query = "INSERT INTO {$this->table} SET " . 
        (!empty($this->subjectTitle) ? " subjectTitle='{$this->subjectTitle}' " : null) .
        (!empty($this->subjectSlug) ? ", subjectSlug='{$this->subjectSlug}' " : null);

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
        $this->subjectSlug = \Core\Functions::make_slug($this->subjectTitle);

        $query = "UPDATE {$this->table} SET " . 
        (!empty($this->subjectTitle) ? " subjectTitle='{$this->subjectTitle}' " : null) .
        (!empty($this->subjectSlug) ? ", subjectSlug='{$this->subjectSlug}' " : null) .
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