<?php

namespace Controller;

class ClassCategory extends \Core\Controller {

    public function index() {
        $message = [];
        if(isset($_POST['create'])) $message = $this->create();
        if(isset($_POST['update'])) $message = $this->update();
        if(isset($_POST['delete'])) $message = $this->delete();

        $classCategory = $this->classCategory();
        $grading = $this->grading();

        \Core\View::render('classCategory/index.php', compact('message', 'classCategory', 'grading'));
    }

    private function create() { 

        $message = ["No input detected"];

        if(!empty($_POST)) {

            $classCategory = new \Model\ClassCategory();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($classCategory, $key)) $classCategory->$key = $value;
            }

            $create = $classCategory->create();
            if($create === -1){$message = ['Class Category with the provided details already exists.'];}
            if($create === 1){$message = ['Class Category created successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }

    private function update() { 

        $message = ["No input detected"];
        
        if(!empty($_POST)) {

            if(empty($_POST['ID'])) return ['Unable to detect entry. Please contact support.'];

            $classCategory = new \Model\ClassCategory();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($classCategory, $key)) $classCategory->$key = $value;
            }

            $create = $classCategory->update();
            if($create === 1){$message = ['Record updated successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }

    private function read($id = null) { 

        $classCategory = new \Model\ClassCategory();
        $classCategory->ID = $id;

        return $classCategory->read();

    }
    private function delete($id = null) { 

        $message = ["No input detected"];

        if(!empty($_POST['delete'])) {

            $admin = new \Model\ClassCategory();
            $admin->ID = $_POST['delete'];

            $delete = $admin->delete();

            if($delete === 1){$message = ['Class Category deleted successfully'];}
            if($delete === 0){$message = ['The specified Class Category was not found'];}

            return $message;

        }

    }
}