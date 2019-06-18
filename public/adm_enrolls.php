<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: adm_enrolls.php
 * Student: Alessandra Diniz
 * Date: May/16/2019
 */

//Variables declaration
$page_title = 'Enrolls - Admin';
$class_admin = 'admin';

//Include Config file
include __DIR__ . '/../conf/config.php';

//Uses students enrolled in classes
use classes\Registers;

//Create a new instance of Registers class
$tbData = new Registers($dbh);

//Define list of fields for Enrolls Admin Page
$table_fields = ['student_name', 'class_name', 'week_days', 'begin_time'];
$table_key = 'id_schedule';


//Include Administrative Page
include __DIR__ . '/../inc/adm_page.php';
