<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: login.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Login';

$loggedin = false;

//Define required fields
//This defition will be used to show "*" before the label
$required = ['email', 'password'];

//Include Config File
include __DIR__ . '/../conf/config.php';

//Required Validator File and Functions File
require __DIR__ . '/../lib/functions.php';

use classes\Validator;

$v = new Validator();

//Verify if it is a POST request,
if ('POST' == $_SERVER['REQUEST_METHOD']) {
    //Verify if required was filled
    $v->required($required);

    $v->validateEmail('email');

    $v->validateLogin('email', 'password', $dbh);

    $errors = $v->errors();

    // If there is no errors, inserts the user
    if (!$errors) {
        //Regenerate new session id when user loggedin
        session_regenerate_id();

        $_SESSION['flash_msg'] = array(
          'type' => 'success',
          'message' => "Welcome back, {$_SESSION['first_name']}! You have successfuly logged in!");
        header('Location: profile.php');
        die();
    } else {
        unset($_POST['password']);
    }
}

?><!doctype html>

<!-- Head -->
<?php include __DIR__ . '/../inc/head_inc.php';?> 
      
    <div id="wrapper">  
      
        <!-- Main Page -->
        <main> 
        <!-- If user is loggedin with sucess, go to Profile page -->
        <?php if ($loggedin) : ?>
            <?php

            ?>

        <!-- If user is not loggedin, shows Form -->
        <?php else : ?>
            <form method="post"
                  action="<?=filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING)?>"
                  autocomplete="on"
                  novalidate>
             
                <h1 class="form_title"><?=$page_title?></h1>

                <input type="hidden" name="csrf_token" value="<?=getToken();?>"/> 
                <span class="error"><?=(!empty($errors['csrf_token']) ? $errors['csrf_token'] : '') ?></span>

                <p>
                    <label for="email" 
                    class=<?=(in_array('email', $required) ? 'required' : '') ?>>Email Address</label> <br/>
                    <input type="email" 
                           name="email" 
                           id="email"
                           class="medium_field"
                           placeholder="Enter your email"
                           value="<?=clean('email')?>"
                    />
                    <span class="error"><?=(!empty($errors['email']) ? $errors['email'] : '') ?></span>
                </p>

                <p>
                    <label for="password" 
                    class=<?=(in_array('password', $required) ? 'required' : '') ?>>Password</label> <br/>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="small_field"
                           placeholder="Enter your password"
                           value="<?=(empty($errors) ? clean('password') : '') ?>"
                    />
                    <span class="error"><?=(!empty($errors['password']) ? $errors['password'] : '') ?></span>
                </p>

                <p>
                    <input value="Login" type="submit" />
                    <input value="Clear Form" type="reset" />
                </p>
              

            </form>
        <?php endif; ?>
          
        </main>
      
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
