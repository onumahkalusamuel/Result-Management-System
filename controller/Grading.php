<?php

namespace Controller;

class Grading extends \Core\Controller {

    public function index() {
        $message = [];
        if(isset($_POST['create'])) $message = $this->create();
        if(isset($_POST['update'])) $message = $this->update();
        if(isset($_POST['delete'])) $message = $this->delete();

        $grading = $this->grading();

        \Core\View::render('grading/index.php', compact('message', 'grading'));
    }

    private function create() { 

        $message = ["No input detected"];

        if(!empty($_POST)) {

            $grading = new \Model\Grading();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($grading, $key)) $grading->$key = $value;
            }

            $create = $grading->create();
            if($create === -1){$message = ['Grading with the provided details already exists.'];}
            if($create === 1){$message = ['Grading created successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }
    private function update() { 

        $message = ["No input detected"];
        
        if(!empty($_POST)) {

            if(empty($_POST['ID'])) return ['Unable to detect entry. Please contact support.'];

            $grading = new \Model\Grading();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($grading, $key)) $grading->$key = $value;
            }

            $create = $grading->update();
            if($create === 1){$message = ['Record updated successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }

    private function delete($id = null) {

        $message = ["No input detected"];

        if(!empty($_POST['delete'])) {

            $grading = new \Model\Grading();
            $grading->ID = $_POST['delete'];

            $delete = $grading->delete();

            if($delete === 1){$message = ['Grading deleted successfully'];}
            if($delete === 0){$message = ['The specified Grading was not found'];}

            return $message;

        }

    }
}