<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: logout.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Logout';

//Include Config File
include __DIR__ . '/../conf/config.php';

//Verify if the user is logged
if (empty($_SESSION['id_user']) or $_SESSION['id_user'] == 0) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "You need to be logged in to execute a logout!");
    header('Location: login.php');
    die();
}

//Clean $_SESSION variables and regenerate id session
unset($_SESSION['first_name']);
unset($_SESSION['id_user']);
unset($_SESSION['admin']);
unset($_SESSION['is_enrolled']);
session_regenerate_id();

$_SESSION['flash_msg'] = array(
    'type' => 'success',
    'message' => "You have successfuly logged out!");
header('Location: login.php');
die();
