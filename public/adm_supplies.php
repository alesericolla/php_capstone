<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: adm_supplies.php
 * Student: Alessandra Diniz
 * Date: May/16/2019
 */

//Variables declaration
$page_title = 'Supplies - Admin';
$class_admin = 'admin';

//Include Config file
include __DIR__ . '/../conf/config.php';

//Uses supplies class
use classes\Supplies;

//Create a new instance of Supplies class
$tbData = new Supplies($dbh);

//Define list of fields for Fees Admin Page
$table_fields = ['id_supplies', 'short_description'];
$table_key = 'id_supplies';


//Include Administrative Page
include __DIR__ . '/../inc/adm_page.php';
