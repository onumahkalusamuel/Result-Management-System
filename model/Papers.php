<?php
namespace Model;

class Papers {
    // database table
    private $table = 'papers', 
    $classCategoryTable = 'classcategory',
    $subjectTable = 'subjects',
    $conn;
    
    private $DBTableSchema = 
        "CREATE TABLE IF NOT EXISTS 
        `papers`(
            `ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `paperTitle` varchar(100) NOT NULL,
            `paperSlug` varchar(100) NOT NULL,
            `classCategoryID` int(11) NOT NULL,
            `subjectID` int(11) NOT NULL,
            `maximumScore` int(11) DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    
    // properties declaration
    public $ID, $paperTitle, $paperSlug, $classCategoryID, $subjectID, $maximumScore;

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

        $query = "SELECT p.*, cC.classCategoryTitle, s.subjectTitle 
        FROM {$this->table} AS p 
        LEFT JOIN
        ({$this->classCategoryTable} AS cC, {$this->subjectTable} AS s)
        ON p.classCategoryId = cC.ID
        AND p.subjectID =  s.ID
        WHERE 1 " . 
        (!empty($this->ID) ? " AND p.ID={$this->ID}" : null) .
        (!empty($this->paperTitle) ? " AND p.paperTitle='{$this->paperTitle}'" : null) .
        (!empty($this->classCategoryID) ? " AND p.classCategoryID='{$this->classCategoryID}'" : null) .
        (!empty($this->subjectID) ? " AND p.subjectID='{$this->subjectID}'" : null) .
        (!empty($this->maximumScore) ? " AND p.maximumScore='{$this->maximumScore}'" : null);

        $stmt = $db->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) $return[] = $row;

        return $return;

    }

    // create
    public function create() {

        global $db;

        if(empty($this->paperTitle)) return 0;

        //check existence
        $query = "SELECT ID FROM {$this->table} WHERE paperTitle='{$this->paperTitle}' AND classCategoryID='{$this->classCategoryID}' AND subjectID='{$this->subjectID}'";
        
        $stmt = $db->prepare($query);
        $stmt->execute();

        if($stmt->rowCount() > 0 ) {
            $this->ID = $stmt->fetch(\PDO::FETCH_ASSOC)['ID'];
            return $this->update();
        }

        //continue
        $this->paperSlug = \Core\Functions::make_slug($this->paperTitle);

        $query = "INSERT INTO {$this->table} SET " . 
        (!empty($this->paperTitle) ? " paperTitle='{$this->paperTitle}' " : null).
        (!empty($this->paperSlug) ? ", paperSlug='{$this->paperSlug}' " : null).
        (!empty($this->classCategoryID) ? ", classCategoryID='{$this->classCategoryID}' " : null).
        (!empty($this->subjectID) ? ", subjectID='{$this->subjectID}' " : null).
        (!empty($this->maximumScore) ? ", maximumScore='{$this->maximumScore}' " : null);

        $stmt = $db->prepare($query);
        $stmt->execute();

        if($stmt->rowCount()==1) return 1;
        
        printf("Error: %s, \n", json_encode($stmt->errorInfo()));
        return 0;

    }
    // update
    public function update() {

        global $db;

        if(empty($this->ID) || empty($this->paperTitle)) return 0;
        
        //continue
        $this->paperSlug = \Core\Functions::make_slug($this->paperTitle);
        
        $query = "UPDATE {$this->table} SET " . 
        (!empty($this->paperTitle) ? " paperTitle='{$this->paperTitle}' " : null).
        (!empty($this->paperSlug) ? ", paperSlug='{$this->paperSlug}' " : null).
        (!empty($this->classCategoryID) ? ", classCategoryID='{$this->classCategoryID}' " : null).
        (!empty($this->subjectID) ? ", subjectID='{$this->subjectID}' " : null).
        (!empty($this->maximumScore) ? ", maximumScore='{$this->maximumScore}' " : null).
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