<?php

namespace Controller;

class Admin extends \Core\Controller {

    public function index() {
        $message = [];
        if(isset($_POST['create'])) $message = $this->create();
        if(isset($_POST['update'])) $message = $this->update();
        if(isset($_POST['delete'])) $message = $this->delete();

        $admins = $this->admin();

        \Core\View::render('admin/index.php', compact('message', 'admins'));
    }
    private function create() { 

        $message = ["No input detected"];

        if(!empty($_POST)) {

            $admin = new \Model\Admin();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($admin, $key)) $admin->$key = $value;
            }

            $create = $admin->create();
            if($create === -1){$message = ['User with the provided details already exists.'];}
            if($create === 1){$message = ['User created successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }
    private function update() { 

        $message = ["No input detected"];
        
        if(!empty($_POST)) {

            if(empty($_POST['ID'])) return ['Unable to detect user. Please contact support.'];

            $admin = new \Model\Admin();
            foreach($_POST as $key => $value) {
                if (!empty($value) && property_exists($admin, $key)) $admin->$key = $value;
            }
            //remove the unnecessary ones
            $admin->userName = null;

            $create = $admin->update();
            if($create === 1){$message = ['User updated successfully'];}
            if($create === 0){$message= ['An error occured. Please try again'];}

            return $message;

        }
    }
    
    private function delete() { 

        $message = ["No input detected"];

        if(!empty($_POST['delete'])) {

            if($_POST['delete'] == '1')  return ['You cannot delete the Super Admin'];

            $admin = new \Model\Admin();
            $admin->ID = $_POST['delete'];

            $delete = $admin->delete();

            if($delete === 1){$message = ['User deleted successfully'];}
            if($delete === 0){$message = ['The specified user was not found'];}

            return $message;

        }

    }
    public function login() {

        $message = [];

        if(isset($_POST['login'])) {

            $returnURL = "admin";

            $userName = $_POST['userName'];
            $password = $_POST['password'];

            if(!empty($userName) && !empty($password)) {

                $user = $this->adminLogin(['userName'=>$userName]);
                
                if(!empty($user)) {

                    if(password_verify($password, $user['password'])) {

                        $_SESSION['userName'] = $userName;
                        $_SESSION['ID'] = $user['ID'];
                        $_SESSION['lastName'] = $user['lastName'];

                        if(!empty($_SESSION['returnURL'])) $returnURL = $_SESSION['returnURL'];

                        header("Location: {$returnURL}");

                    }
                }
            }

            $message = ["Invalid Username / Password Comnination"];
        }

        \Core\View::render('Login.php', compact('message'));
    }
    public function logout() {

        $message = [];
        foreach($_SESSION as $key => $value) unset($_SESSION[$key]);

        session_destroy();

        header("Location: login");
        
    }
}