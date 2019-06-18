<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: profile.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Profile';

//Include Config File
include __DIR__ . '/../conf/config.php';

//Required Functions File
require __DIR__ . '/../lib/functions.php';

//Verify if the user is logged
if (empty($_SESSION['id_user']) or $_SESSION['id_user'] == 0) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "You need to be logged in to access your Profile!");
    header('Location: login.php');
    die();
}

try {
    // create query for users table
    $query = "SELECT u.id_user, 
              first_name, 
              last_name,
              email,
              phone,
              street,
              city,
              province,
              country,
              postal_code,
              birthday,
              areyou,
              parent_guardian,
              parent_guardian_phone, 
              automatic_payment,
              resume
              FROM users u
              LEFT JOIN students s on (u.id_user = s.id_user)
              LEFT JOIN instructors i on (u.id_user = i.id_user)
              WHERE u.id_user = :id_user";

    $params = array(
        ':id_user' => $_SESSION['id_user']
    );

    // prepare query
    $stmt = $dbh->prepare($query);

    // execute query
    $stmt->execute($params);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die($e->getMessage());
}

?><!doctype html>
 
<!-- Head --> 
<?php include __DIR__ . '/../inc/head_inc.php';?> 
        
    <div id="wrapper">    
        
        <!-- Main Page -->
        <main> 
                        
            <h1><?=$page_title?></h1>

            <p>Here is your profile:</p> 

            <div>             
                <ul>
                    <li><strong>First Name</strong>: <?=esc($result['first_name'])?></li>
                    <li><strong>Last Name</strong>: <?=esc($result['last_name'])?></li>
                    <li><strong>Email</strong>: <?=esc($result['email'])?></li>
                    <li><strong>Phone</strong>: <?=esc($result['phone'])?></li>
                    <li><strong>Street</strong>: <?=esc($result['street'])?></li>
                    <li><strong>City</strong>: <?=esc($result['city'])?></li>
                    <li><strong>Province</strong>: <?=esc($result['province'])?></li>
                    <li><strong>Country</strong>: <?=esc($result['country'])?></li>
                    <li><strong>Postal Code</strong>: <?=esc($result['postal_code'])?></li>
                    <li><strong>Birthday</strong>: <?=substr(esc($result['birthday']), 0, 10);?></li>
                    <li><strong>Are you</strong>: 
                            <?=(strpos(esc($result['areyou']), 'student') === false) ? '' : 'Student;' ?>
                            <?=(strpos(esc($result['areyou']), 'instructor') === false ) ? '' :'Instructor; '    ?>
                            <?=(strpos(esc($result['areyou']), 'other') === false ) ? '' : 'Other ' ?>
                    </li>
                    <?php if (!(strpos(esc($result['areyou']), 'student') === false)) : ?>
                        <li><strong>Parent/Guardian Name</strong>: <?=esc($result['parent_guardian'])?></li>
                        <li><strong>Parent/Guardian Phone</strong>: <?=esc($result['parent_guardian_phone'])?></li>
                        <li><strong>Automatic Payment Autorized</strong>: 
                          <?=(esc($result['automatic_payment'])==1?'Yes':'No')?></li>
                    <?php endif; ?>
                    <?php if (!(strpos(esc($result['areyou']), 'instructor') === false)) : ?>
                        <li><strong>Resume</strong>: <?=esc($result['resume'])?></li>
                    <?php endif; ?>


                    
                </ul>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
