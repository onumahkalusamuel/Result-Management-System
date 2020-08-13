<?php

namespace Controller;

class GradingSystem extends \Core\Controller {

    public function index() {
        $message = $gradingSystem = [];
        if(isset($_POST['create'])) $message = $this->create();
        if(isset($_POST['update'])) $message = $this->update();
        if(isset($_POST['delete'])) $message = $this->delete();
        if(isset($_POST['fetchGradingItems'])) $gradingSystem = $this->gradingSystem(['gradingID'=>$_POST['gradingID']]);

        $grading = $this->grading();

        \Core\View::render('gradingSystem/index.php', compact('message', 'gradingSystem', 'grading'));
    }

    private function create() { 

        $message = ["No input detected"];

        if(!empty($_POST)) {

            $gradingSystem = new \Model\GradingSystem();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($gradingSystem, $key)) $gradingSystem->$key = $value;
            }

            $create = $gradingSystem->create();
            if($create === -1){$message = ['Grading System with the provided details already exists.'];}
            if($create === 1){$message = ['Grading System created successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }
    private function update() { 

        $message = ["No input detected"];
        
        if(!empty($_POST)) {

            if(empty($_POST['ID'])) return ['Unable to detect user. Please contact support.'];
            
            $gradingSystem = new \Model\GradingSystem();
            foreach($_POST as $key => $value) {
                
                if (!empty($value) && property_exists($gradingSystem, $key)) {$gradingSystem->$key = $value;}
            }

            $create = $gradingSystem->update();
            if($create === 1){$message = ['Record updated successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }
    private function read($id = null) { 

        $gradingSystem = new \Model\GradingSystem();
        $gradingSystem->ID = $id;
        return $gradingSystem->read();

    }
    private function delete($id = null) { 

        $message = ["No input detected"];

        if(!empty($_POST['delete'])) {

            $gradingSystem = new \Model\GradingSystem();
            $gradingSystem->ID = $_POST['delete'];

            $delete = $gradingSystem->delete();

            if($delete === 1){$message = ['Grading System deleted successfully'];}
            if($delete === 0){$message = ['The specified user was not found'];}

            return $message;

        }

    }
}