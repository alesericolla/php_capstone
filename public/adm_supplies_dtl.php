<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: admin_supplies_dtl.php
 * Student: Alessandra Diniz
 * Date: May/26/2019
 */

//Variables declaration
$page_title = 'Supplies - Admin';
$class_admin = 'admin';

use classes\Supplies;
use classes\Validator;

//Include Config File
include __DIR__ . '/../conf/config.php';
include __DIR__ . '/../lib/functions.php';

//Verify if the user is logged as an Admin user
if (empty($_SESSION['admin']) or $_SESSION['admin'] == 0) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "You need to be logged in as an Admin user to access this page!");
    header('Location: login.php');
    die();
}

$action = filter_input(INPUT_POST, 'action');
$action_dtl = filter_input(INPUT_POST, 'action_dtl');

//Verify if there is an action defined, sent by Supplies - Admin page (action)
if (empty($action)) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "This page is accessed only by Supplies - Admin page!");
    header('Location: classes.php');
    die();
}


if ($action=='Delete') {
    $read_only = 'readonly';
    $read_only_id = 'readonly';
} else {
    $read_only = '';
    if ($action=='Edit') {
        $read_only_id = 'readonly';
    } else {
        $read_only_id = '';
    }
}

//Define required fields
//This defition will be used to show "*" before the label
$required = ['id_supplies', 'required_supplies'];

$id_supplies = filter_input(INPUT_POST, 'id_supplies');

$v = new Validator();

//Page called by POST in itself
if ("POST"== $_SERVER["REQUEST_METHOD"] and !empty($action_dtl) and
     $action_dtl == 'POST_ITSELF') {
    //If action_dtl is Cancel, return to previous page
    if ($action=="Cancel") {
        header('Location: adm_supplies.php');
        die();
    }

    //Verify if required fields was filled
    if ($action!="Delete") {
        $v->required($required);
        $v->validateIdSupplies('id_supplies');
    }

    $errors = $v->errors();

    // If there is no errors, inserts the Supplies
    if (!$errors) {
        //If action is Cancel, delete the register
        if ($action=="Delete") {
            // create query to update deleted field in supplies table
            $query = "UPDATE 
                      supplies
                      set deleted = 1, 
                      updated_at = now()
                      WHERE 
                        id_supplies = :id_supplies";

            // create parameters array
            $params = array(
                ':id_supplies' => filter_input(INPUT_POST, 'id_supplies')
            );

            $msg = "Supplies deleted!";
        }



        //If action is Add New, insert new register
        if ($action=="Add") {
            // create query to insert supplies table
            $query = "INSERT INTO 
                      supplies
                      (id_supplies, required_supplies)
                     VALUES
                        (:id_supplies, :required_supplies)";

            // create parameters array
            $params = array(
                ':id_supplies' => filter_input(INPUT_POST, 'id_supplies'),
                ':required_supplies' => filter_input(INPUT_POST, 'required_supplies')
            );

            $msg = "Supplies inserted!";
        }

        //If action is Edit New, update the register
        if ($action=="Edit") {
            // create query to update supplies table
            $query = "UPDATE 
                      supplies SET 
                      required_supplies = :required_supplies, 
                      updated_at = now()
                    WHERE 
                        id_supplies = :id_supplies";

            // create parameters array
            $params = array(
                ':id_supplies' => filter_input(INPUT_POST, 'id_supplies'),
                ':required_supplies' => filter_input(INPUT_POST, 'required_supplies')
            );

            $msg = "Supplies updated!";
        }

        // execute query
        execute_query($dbh, $query, $params);

        $_SESSION['flash_msg'] = array(
            'type' => 'success',
            'message' => $msg);
        header('Location: adm_supplies.php');
        die();
    }
}

//Get supplies record details for the id selected
$tbsupplies = new supplies($dbh);
$supplies_detail = $tbsupplies->find($id_supplies);

?><!doctype html>
 
<!-- Head --> 
<?php include __DIR__ . '/../inc/head_inc.php';?> 
        
<!-- Admin Navigation --> 
<?php include __DIR__ . '/../inc/admin_inc.php';?> 

    <div id="wrapper">    
        
        <!-- Main Page -->
        <main> 
                        
            <?php
                echo "<h1>{$page_title}</h1>";
            ?>

            <form method="post"
                  action="adm_supplies_dtl.php"
                  autocomplete="on"
                  novalidate>

                <input type="hidden" name="csrf_token" value="<?=getToken();?>"/> 
                <span class="error">
                    <?=(!empty($errors['csrf_token']) ? $errors['csrf_token'] : '') ?></span> 

                <p>
                    <label for="id_supplies" 
                    class=<?=(in_array('id_supplies', $required) ? 'required' : '') ?>
                    >Id Supplies</label> <br/>
                    <input type="text" 
                           name="id_supplies" 
                           id="id_supplies" 
                           class="small2_field"
                           placeholder="Enter id supplies"
                            <?=$read_only_id?>
                           value="<?=previous_value('id_supplies', $supplies_detail)?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['id_supplies']) ? $errors['id_supplies'] : '') ?></span>
                </p>

                <p>
                    <label for="required_supplies" 
                    class=<?=(in_array('required_supplies', $required) ? 'required' : '') ?>
                    >Required Supplies</label> <br/>
                    <textarea rows="5"  
                           name="required_supplies" 
                           id="required_supplies" 
                           class="large_field"
                           placeholder="Enter required supplies"
                            <?=$read_only?>
                    /><?=previous_value('required_supplies', $supplies_detail)?></textarea>
                    <span class="error">
                        <?=(!empty($errors['required_supplies']) ? $errors['required_supplies'] : '') ?></span>
                </p>

                <p>
                    <input type="submit" 
                           name="action"
                           value=<?=$action?>
                    />
                    <input type="submit" 
                           name="action"
                           value="Cancel"
                    />  
                    <input type="hidden" 
                           name="action_dtl"
                           value="POST_ITSELF"
                    />                                                            
                </p>
            </form>
        </main>
        
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
