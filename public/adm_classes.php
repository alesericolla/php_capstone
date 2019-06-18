<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: adm_classes.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Classes - Admin';
$class_admin = 'admin';

//Include Config file
include __DIR__ . '/../conf/config.php';

//Uses Classes class
use classes\Classes;

//Create a new instance of Classes class
$tbData = new Classes($dbh);

//Define list of fields for Classes Admin Page
$table_fields = ['class_name', 'short_description'];
$table_key = 'id_class';


//Include Administrative Page
include __DIR__ . '/../inc/adm_page.php';
