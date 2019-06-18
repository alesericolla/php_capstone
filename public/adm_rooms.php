<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: adm_rooms.php
 * Student: Alessandra Diniz
 * Date: May/16/2019
 */

//Variables declaration
$page_title = 'Rooms - Admin';
$class_admin = 'admin';

//Include Config file
include __DIR__ . '/../conf/config.php';

//Uses Rooms class
use classes\Rooms;

//Create a new instance of Rooms class
$tbData = new Rooms($dbh);

//Define list of fields for Rooms Admin Page
$table_fields = ['room_name', 'max_students'];
$table_key = 'id_room';


//Include Administrative Page
include __DIR__ . '/../inc/adm_page.php';
