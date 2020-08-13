<?php

namespace Controller;

class Students extends \Core\Controller {

    public function index() {
        $message = $students = [];
        if(isset($_POST['create'])) $message = $this->create();
        if(isset($_POST['update'])) $message = $this->update();
        if(isset($_POST['delete'])) $message = $this->delete();
        if(isset($_POST['fetchStudents'])) $students = $this->students(['classCategoryID'=>$_POST['classCategoryID']]);
        
        $classCategory = $this->classCategory();

        \Core\View::render('students/index.php', compact('message', 'students', 'classCategory'));
    }
    private function create() { 

        $message = ["No input detected"];

        if(!empty($_POST)) {

            $students = new \Model\Students();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($students, $key)) $students->$key = $value;
            }

            $create = $students->create();
            if($create === -1){$message = ['Student with the provided details already exists.'];}
            if($create === 1){$message = ['Student created successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }
    private function update() { 

        $message = ["No input detected"];
        
        if(!empty($_POST)) {

            if(empty($_POST['ID'])) return ['Unable to detect user. Please contact support.'];

            $students = new \Model\Students();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($students, $key)) $students->$key = $value;
            }

            $create = $students->update();
            if($create === 1){$message = ['Record updated successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }
    private function delete($id = null) { 

        $message = ["No input detected"];

        if(!empty($_POST['delete'])) {

            $students = new \Model\Students();
            $students->ID = $_POST['delete'];

            $delete = $students->delete();

            if($delete === 1){$message = ['Student deleted successfully'];}
            if($delete === 0){$message = ['The specified user was not found'];}

            return $message;

        }

    }

    public function upgrade(){

        if(!empty($_POST['class'])) $message = $this->upgradeStudents($_POST['class']);

        $classCategory = $this->classCategory();

        \Core\View::render('students/upgrade.php', compact('message', 'classCategory'));

    }

    private function upgradeStudents($data) {

        $toProcess = [];
        $errorCount = 0;

        // prepare the posted data. removed classes where there is no change
        foreach($data as $dKey => $dData) {
            
            if ($dData['from'] === $dData['to']) continue;

            $toProcess[$dKey] = $dData;
            $toProcess[$dKey]['students'] = [];

        }

        if(empty($toProcess)) return false;
        
        $students = $this->students();
        if(empty($students)) return false;

        foreach($students as $student) {

            $sKey = $student['ID'];
            $sClass = $student['classCategoryID'];
            
            if(array_key_exists($sClass, $toProcess)) $toProcess[$sClass]['students'][] = $sKey;
        }

        $studentsModel = new \Model\Students();
        //do the actual transfer
        foreach($toProcess as $tpKey => $tpData) {
            if(!$studentsModel->upgrade($tpData['to'], implode(", ", $tpData['students']))) $errorCount++;
        }

        return $message = [ 'Upgrade successful. Error count: ('.$errorCount.')' ];
        
    }
}