<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: adm_fees.php
 * Student: Alessandra Diniz
 * Date: May/16/2019
 */

//Variables declaration
$page_title = 'Fees - Admin';
$class_admin = 'admin';

//Include Config file
include __DIR__ . '/../conf/config.php';

//Uses Fees class
use classes\Fees;

//Create a new instance of Fees class
$tbData = new Fees($dbh);

//Define list of fields for Fees Admin Page
$table_fields = ['fee_name', 'monthly_fee'];
$table_key = 'id_fee';


//Include Administrative Page
include __DIR__ . '/../inc/adm_page.php';
