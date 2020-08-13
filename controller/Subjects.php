<?php

namespace Controller;

class Subjects extends \Core\Controller {

    public function index() {
        $message = [];
        if(isset($_POST['create'])) $message = $this->create();
        if(isset($_POST['update'])) $message = $this->update();
        if(isset($_POST['delete'])) $message = $this->delete();

        $subjects = $this->read();

        \Core\View::render('subjects/index.php', compact('message', 'subjects'));
    }

    private function create() { 

        $message = ["No input detected"];

        if(!empty($_POST)) {

            $subjects = new \Model\Subjects();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($subjects, $key)) $subjects->$key = $value;
            }

            $create = $subjects->create();
            if($create === -1){$message = ['Subject with the provided details already exists.'];}
            if($create === 1){$message = ['Subject created successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }
    private function update() { 

        $message = ["No input detected"];
        
        if(!empty($_POST)) {

            if(empty($_POST['ID'])) return ['Unable to detect entry. Please contact support.'];

            $subjects = new \Model\Subjects();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($subjects, $key)) $subjects->$key = $value;
            }

            $create = $subjects->update();
            if($create === 1){$message = ['Record updated successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }
    private function read($id = null) { 

        $subjects = new \Model\Subjects();
        $subjects->ID = $id;

        return $subjects->read();

    }
    private function delete($id = null) { 

        $message = ["No input detected"];

        if(!empty($_POST['delete'])) {

            $admin = new \Model\Subjects();
            $admin->ID = $_POST['delete'];

            $delete = $admin->delete();

            if($delete === 1){$message = ['Subject deleted successfully'];}
            if($delete === 0){$message = ['The specified Subject was not found'];}

            return $message;

        }

    }
}