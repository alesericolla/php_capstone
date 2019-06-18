<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Configuration File
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Start the PHP Session
session_start();

//Output buffering
ob_start();

//If the session csrf_token is empty then set a session CSRF token
if (empty($_SESSION['csrf_token'])) {
    //set a token in session
    $_SESSION['csrf_token']=md5(rand());
}

//If this is a POST request
if ('POST' == $_SERVER['REQUEST_METHOD']) {
    $token_post = filter_input(INPUT_POST, 'csrf_token');

    //If there's a crsf_token field in the form
    if (!empty($token_post)) {
        $token = $_SESSION['csrf_token'];

        //Verify if the token is valid (equal to session token)
        if ($token != $token_post) {
            die('CSRF token mismatch.');
        }
    }
}

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

//Variables declaration
$site_name='Bella Dance School';
$errors = [] ;
$success = false;


spl_autoload_register('my_autoload');


/**
 * Execute autoload for classes
 * @param  [String] $class [Class name]
 * @return Void
 */
function my_autoload($class)
{

    $class = trim($class, '\\');
    $class = str_replace('\\', '/', $class);
    $class = $class . '.php';
    $file = __DIR__ . '/' . $class;
    $file = str_replace('\conf', '', $file);
    //remove /conf again for servers different of windows
    $file = str_replace('/conf', '', $file);

    if (file_exists($file)) {
        require $file;
    }
}

//Insert connect file
require __DIR__ . '/../conf/connect.php';

//Insert log file
require __DIR__ . '/../conf/log.php';
