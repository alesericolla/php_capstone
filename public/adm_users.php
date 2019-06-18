<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: adm_users.php
 * Student: Alessandra Diniz
 * Date: May/16/2019
 */

//Variables declaration
$page_title = 'Users - Admin';
$class_admin = 'admin';

//Include Config file
include __DIR__ . '/../conf/config.php';

//Uses users class
use classes\Users;

//Create a new instance of Users class
$tbData = new Users($dbh);

//Define list of fields for Users Admin Page
$table_fields = ['first_name', 'last_name', 'email', 'phone'];
$table_key = 'id_user';


//Include Administrative Page
include __DIR__ . '/../inc/adm_page.php';
