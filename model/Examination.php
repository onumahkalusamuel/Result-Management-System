<?php
namespace Model;

class Examination {
    // database table
    private $table = 'examination', $conn;
    private $DBTableSchema = 
        "CREATE TABLE IF NOT EXISTS 
        `examination`(
            `ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `examinationTitle` varchar(100) NOT NULL,
            `examinationSlug` varchar(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    
    // properties declaration
    public $ID, $examinationTitle, $examinationSlug ;

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
        (!empty($this->examinationTitle) ? " AND examinationTitle='{$this->examinationTitle}'" : null) .
        (!empty($this->examinationSlug) ? " AND examinationSlug='{$this->examinationSlug}'" : null);
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
        $this->examinationSlug = \Core\Functions::make_slug($this->examinationTitle);

        $query = "INSERT INTO {$this->table} SET " . 
        (!empty($this->examinationTitle) ? " examinationTitle='{$this->examinationTitle}' " : null) .
        (!empty($this->examinationSlug) ? ", examinationSlug='{$this->examinationSlug}' " : null);

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
        $this->examinationSlug = \Core\Functions::make_slug($this->examinationTitle);

        $query = "UPDATE {$this->table} SET " . 
        (!empty($this->examinationTitle) ? " examinationTitle='{$this->examinationTitle}' " : null) .
        (!empty($this->examinationSlug) ? ", examinationSlug='{$this->examinationSlug}' " : null) .
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