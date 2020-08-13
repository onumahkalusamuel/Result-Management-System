<?php

namespace Core;

/**
 * Base controller
 *
 * PHP version 7.0
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
        $this->checkLogin();
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }


    // check login

    protected function checkLogin() {
        if(
            $_SERVER['QUERY_STRING'] !== 'login' &&
            (
                empty($_SESSION['userName'])
                || empty($_SESSION['ID'])
                || empty($_SESSION['lastName'])
            )
            ) {

                $_SESSION['returnURL'] = $_SERVER['QUERY_STRING'];

                \Core\View::render('Login.php', compact('message'));

                die();
            }


    }

    // other methods
    protected function admin($params = array()) { 
        $admin = new \Model\Admin();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($admin, $key)) $admin->$key = $value;
        }
        return $admin->read();
    }

    protected function adminLogin($params = array()) { 
        $admin = new \Model\Admin();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($admin, $key)) $admin->$key = $value;
        }
        return $admin->readLogin();
    }

    protected function classCategory($params = array()) { 
        $classCategory = new \Model\ClassCategory();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($classCategory, $key)) $classCategory->$key = $value;
        }
        return $classCategory->read();
    }

    protected function examination($params = array()) { 
        $examination = new \Model\Examination();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($examination, $key)) $examination->$key = $value;
        }
        return $examination->read();
    }

    protected function gradingSystem($params = array()) { 
        $gradingSystem = new \Model\GradingSystem();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($gradingSystem, $key)) $gradingSystem->$key = $value;
        }
        return $gradingSystem->read();
    }

    protected function grading($params = array()) { 
        $grading = new \Model\Grading();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($grading, $key)) $grading->$key = $value;
        }
        return $grading->read();
    }
    
    protected function papers($params = array()) { 
        $papers = new \Model\Papers();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($papers, $key)) $papers->$key = $value;
        }
        return $papers->read();
    }
    
    protected function results($params = array()) { 
        $results = new \Model\Results();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($results, $key)) $results->$key = $value;
        }
        return $results->read();
    }

    protected function scores($params = array()) { 
        $scores = new \Model\Scores();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($scores, $key)) $scores->$key = $value;
        }
        return $scores->read();
    }

    protected function students($params = array()) { 
        $students = new \Model\Students();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($students, $key)) $students->$key = $value;
        }
        return $students->read();
    }

    protected function subjects($params = array()) { 
        $subjects = new \Model\Subjects();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($subjects, $key)) $subjects->$key = $value;
        }
        return $subjects->read();
    }
}
