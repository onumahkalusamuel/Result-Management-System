<?php
namespace Model;

class Results {
    // database table
    private $table = 'results', 
    $studentsTable = 'students',
    $subjectsTable = 'subjects',
    $classCategoryTable = 'classCategory',
    $examinationTable = 'examination',
    $conn;

    private $DBTableSchema = 
        "CREATE TABLE IF NOT EXISTS 
        `results`(
            `ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `resultType` varchar(50) DEFAULT NULL,
            `studentID` int(11) DEFAULT NULL,
            `subjectID` int(11) DEFAULT NULL,
            `classCategoryID` int(11) DEFAULT NULL,
            `examinationID` int(11) NOT NULL,
            `content` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    
    // properties declaration
    public $ID, $resultType, $content;
    public $studentID = 0;
    public $subjectID = 0;
    public $classCategoryID = 0;
    public $examinationID = 0;

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
        r.*, 
        stu.lastName,
        stu.firstName,
        stu.middleName,
        sub.subjectTitle,
        c.classCategoryTitle,
        e.examinationTitle
        FROM {$this->table} AS r
        LEFT JOIN ({$this->studentsTable} AS stu)
        ON r.studentID = stu.ID
        LEFT JOIN ({$this->subjectsTable} AS sub)
        ON r.subjectID = sub.ID
        LEFT JOIN ({$this->classCategoryTable} AS c)
        ON r.classCategoryID = c.ID
        LEFT JOIN ({$this->examinationTable} AS e)
        ON r.examinationID = e.ID
        WHERE 1 " . 
        (!empty($this->ID) ? " AND r.ID='{$this->ID}'" : null) .
        (!empty($this->resultType) ? " AND r.resultType='{$this->resultType}'" : null) .
        (!empty($this->studentID) ? " AND r.studentID='{$this->studentID}'" : null) .
        (!empty($this->subjectID) ? " AND r.subjectID='{$this->subjectID}'" : null) .
        (!empty($this->classCategoryID) ? " AND r.classCategoryID='{$this->classCategoryID}'" : null) .
        (!empty($this->examinationID) ? " AND r.examinationID='{$this->examinationID}'" : null) . 
        " ORDER BY r.examinationID ASC, r.classCategoryID ASC, r.resultType ASC";

        $stmt = $db->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) $return[] = $row;

        return $return;

    }

    // create
    public function create() {

        global $db;

        $read = $this->read();

        if(count($read) > 0 ) {
            $this->ID = $read[0]['ID'];
            return $this->update();
        }

        //continue
        $query = "INSERT INTO {$this->table} SET " . 
        (!empty($this->resultType) ? " resultType='{$this->resultType}' " : null).
        (!empty($this->studentID) ? ", studentID='{$this->studentID}' " : null).
        (!empty($this->subjectID) ? ", subjectID='{$this->subjectID}' " : null).
        (!empty($this->classCategoryID) ? ", classCategoryID='{$this->classCategoryID}' " : null).
        (!empty($this->examinationID) ? ", examinationID='{$this->examinationID}' " : null).
        (!empty($this->content) ? ", content=:content " : null);

        $stmt = $db->prepare($query);

        $stmt->bindParam('content', $this->content);

        $stmt->execute();

        if($stmt->rowCount()==1) return 1;
        
        printf("Error: %s, \n", json_encode($stmt->errorInfo()));
        return 0;

    }
    // update
    public function update() {

        global $db;

        if(empty($this->ID) || empty($this->examinationID)) return 0;

        //continue
        $query = "UPDATE {$this->table} SET " . 
        (!empty($this->resultType) ? " resultType='{$this->resultType}' " : null).
        (!empty($this->studentID) ? ", studentID='{$this->studentID}' " : null).
        (!empty($this->subjectID) ? ", subjectID='{$this->subjectID}' " : null).
        (!empty($this->classCategoryID) ? ", classCategoryID='{$this->classCategoryID}' " : null).
        (!empty($this->examinationID) ? ", examinationID='{$this->examinationID}' " : null).
        (!empty($this->content) ? ", content=:content " : null) .
        " WHERE ID={$this->ID} ";

        $stmt = $db->prepare($query);

        if(!empty($this->content)) $stmt->bindParam('content', $this->content);

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