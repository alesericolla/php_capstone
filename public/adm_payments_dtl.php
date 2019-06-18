<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: adm_payments_dtl.php
 * Student: Alessandra Diniz
 * Date: May/26/2019
 */

//Variables declaration
$page_title = 'Payments - Admin';
$class_admin = 'admin';

//Include Config File
include __DIR__ . '/../conf/config.php';

//Verify if the user is logged as an Admin user
if (empty($_SESSION['admin']) or $_SESSION['admin'] == 0) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "You need to be logged in as an Admin user to access this page!");
    header('Location: login.php');
    die();
}

?><!doctype html>
 
<!-- Head --> 
<?php include __DIR__ . '/../inc/head_inc.php';?> 

<!-- Admin Navigation --> 
<?php include __DIR__ . '/../inc/admin_inc.php';?> 
      
    <div id="wrapper">  
      
        <!-- Main Page -->
        <main> 
                
            <div class="coming_soon">
                <h1><?=$page_title?></h1>
                <h2>Coming Soon</h2>
            </div>
          
        </main>
      
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
