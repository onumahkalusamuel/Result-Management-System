<?php

namespace Controller;

class Examination extends \Core\Controller {

    public function index() {
        $message = [];
        if(isset($_POST['create'])) $message = $this->create();
        if(isset($_POST['update'])) $message = $this->update();
        if(isset($_POST['delete'])) $message = $this->delete();

        $examination = $this->examination();

        \Core\View::render('examination/index.php', compact('message', 'examination'));
    }

    private function create() { 

        $message = ["No input detected"];

        if(!empty($_POST)) {

            $examination = new \Model\Examination();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($examination, $key)) $examination->$key = $value;
            }

            $create = $examination->create();
            if($create === -1){$message = ['Examination with the provided details already exists.'];}
            if($create === 1){$message = ['Examination created successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }
    private function update() { 

        $message = ["No input detected"];
        
        if(!empty($_POST)) {

            if(empty($_POST['ID'])) return ['Unable to detect entry. Please contact support.'];

            $examination = new \Model\Examination();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($examination, $key)) $examination->$key = $value;
            }

            $create = $examination->update();
            if($create === 1){$message = ['Record updated successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }

    private function delete($id = null) { 

        $message = ["No input detected"];

        if(!empty($_POST['delete'])) {

            $admin = new \Model\Examination();
            $admin->ID = $_POST['delete'];

            $delete = $admin->delete();

            if($delete === 1){$message = ['Examination deleted successfully'];}
            if($delete === 0){$message = ['The specified Examination was not found'];}

            return $message;

        }

    }
}