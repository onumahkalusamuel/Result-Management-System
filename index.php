<?php

//check for installation
if(!is_file('core/Config.php')) {
    include_once('install.php'); 
    die();
}

//Bring in all the cores
$core = scandir('core/'); array_shift($core); array_shift($core);
if(!empty($core))
    foreach($core as $core)
        if(is_file('core/'.$core) && pathinfo('core/'.$core, PATHINFO_EXTENSION) == 'php') 
            include_once 'core/'.$core;

//Bring in all the model
$model = scandir('model/'); array_shift($model); array_shift($model);
if(!empty($model))
    foreach($model as $model) 
        if(is_file('model/'.$model) && pathinfo('model/'.$model, PATHINFO_EXTENSION) == 'php') 
            include_once 'model/'.$model;

// Bring in the controller classes
$controller = scandir('controller/'); array_shift($controller); array_shift($controller);
if(!empty($controller)) 
    foreach($controller as $controller) 
        if(is_file('controller/'.$controller) && pathinfo('controller/'.$controller, PATHINFO_EXTENSION) == 'php') 
            include_once 'controller/'.$controller;

// get database ready
$db = new \Core\Database();
$db = $db->connect();

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('\Core\Error::errorHandler');
set_exception_handler('\Core\Error::exceptionHandler');

// session
session_start();
/**
 * Routing
 */
$router = new \Core\Router();

// Add the routes
$router->add('/?', ['controller' => 'Admin', 'action' => 'login']);
$router->add('login/?', ['controller' => 'Admin', 'action' => 'login']);
$router->add('logout/?', ['controller' => 'Admin', 'action' => 'logout']);
$router->add('admin/?', ['controller' => 'Admin', 'action' => 'index']);
$router->add('class-category/?', ['controller' => 'ClassCategory', 'action' => 'index']);
$router->add('students/?', ['controller' => 'Students', 'action' => 'index']);
$router->add('subjects/?', ['controller' => 'Subjects', 'action' => 'index']);
$router->add('grading-system/?', ['controller' => 'GradingSystem', 'action' => 'index']);
$router->add('grading/?', ['controller' => 'Grading', 'action' => 'index']);
$router->add('examination/?', ['controller' => 'Examination', 'action' => 'index']);
$router->add('papers/?', ['controller' => 'Papers', 'action' => 'index']);
$router->add('scores/?', ['controller' => 'Scores', 'action' => 'index']);
$router->add('results/?', ['controller' => 'Results', 'action' => 'index']);
$router->add('{controller}/{action}');
    
$router->dispatch($_SERVER['QUERY_STRING']);
