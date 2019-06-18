<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: adm_payments.php
 * Student: Alessandra Diniz
 * Date: May/16/2019
 */

//Variables declaration
$page_title = 'Payments - Admin';
$class_admin = 'admin';

//Include Config file
include __DIR__ . '/../conf/config.php';

//Uses Payments class
use classes\Payments;

//Create a new instance of Payments class
$tbData = new Payments($dbh);

//Define list of fields for Payments Admin Page
$table_fields = ['student_name', 'class_name', 'week_days', 'begin_time', 'payment_month',
                 'payment_value', 'payment_date'];
$table_key = 'id_schedule';


//Include Administrative Page
include __DIR__ . '/../inc/adm_page.php';
