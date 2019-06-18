<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: admin_fees_dtl.php
 * Student: Alessandra Diniz
 * Date: May/26/2019
 */

//Variables declaration
$page_title = 'Fees - Admin';
$class_admin = 'admin';

use classes\Fees;
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

//Verify if there is an action defined, sent by Fees - Admin page (action)
if (empty($action)) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "This page is accessed only by Fees - Admin page!");
    header('Location: classes.php');
    die();
}


if ($action=='Delete') {
    $read_only = 'readonly';
} else {
    $read_only = '';
}

//Define required fields
//This defition will be used to show "*" before the label
$required = ['fee_name', 'monthly_fee'];

$id_fee = filter_input(INPUT_POST, 'id_fee');

$v = new Validator();

//Page called by POST in itself
if ("POST"== $_SERVER["REQUEST_METHOD"] and !empty($action_dtl) and
     $action_dtl == 'POST_ITSELF') {
    //If action_dtl is Cancel, return to previous page
    if ($action=="Cancel") {
        header('Location: adm_fees.php');
        die();
    }

    //Verify if required fields was filled
    if ($action!="Delete") {
        $v->required($required);
        $v->validateDecimal('monthly_fee');
    }

    $errors = $v->errors();

    // If there is no errors, inserts the fee
    if (!$errors) {
        //If action is Cancel, delete the register
        if ($action=="Delete") {
            // create query to update deleted field in fees table
            $query = "UPDATE 
                      fees
                      set deleted = 1, 
                      updated_at = now()
                      WHERE 
                        id_fee = :id_fee";

            // create parameters array
            $params = array(
                ':id_fee' => filter_input(INPUT_POST, 'id_fee')
            );

            $msg = "Fee deleted!";
        }



        //If action is Add New, insert new register
        if ($action=="Add") {
            // create query to insert fees table
            $query = "INSERT INTO 
                      fees
                      (fee_name, 
                      monthly_fee)
                     VALUES
                        (:fee_name, 
                        :monthly_fee)";

            // create parameters array
            $params = array(
                ':fee_name' => filter_input(INPUT_POST, 'fee_name'),
                ':monthly_fee' => filter_input(INPUT_POST, 'monthly_fee')
            );

            $msg = "Fee inserted!";
        }

        //If action is Edit New, update the register
        if ($action=="Edit") {
            // create query to update fees table
            $query = "UPDATE 
                      fees SET 
                      fee_name = :fee_name, 
                      monthly_fee = :monthly_fee,
                      updated_at = now()
                    WHERE 
                        id_fee = :id_fee";

            // create parameters array
            $params = array(
                ':id_fee' => filter_input(INPUT_POST, 'id_fee'),
                ':fee_name' => filter_input(INPUT_POST, 'fee_name'),
                ':monthly_fee' => filter_input(INPUT_POST, 'monthly_fee')
            );

            $msg = "Fee updated!";
        }

        // execute query
        execute_query($dbh, $query, $params);

        $_SESSION['flash_msg'] = array(
            'type' => 'success',
            'message' => $msg);
        header('Location: adm_fees.php');
        die();
    }
}

//Get fees record details for the id selected
$tbfees = new Fees($dbh);
$fees_detail = $tbfees->find($id_fee);

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
                  action="adm_fees_dtl.php"
                  autocomplete="on"
                  novalidate>

                <input type="hidden" name="csrf_token" value="<?=getToken();?>"/> 
                <span class="error">
                    <?=(!empty($errors['csrf_token']) ? $errors['csrf_token'] : '') ?></span>

                <input type="hidden" 
                       name="id_fee" 
                       value="<?=previous_value('id_fee', $fees_detail)?>"/> 

                <p>
                    <label for="fee_name" 
                    class=<?=(in_array('fee_name', $required) ? 'required' : '') ?>
                    >Fee Name</label> <br/>
                    <input type="text" 
                           name="fee_name" 
                           id="fee_name" 
                           class="small_field"
                           placeholder="Enter fee name"
                            <?=$read_only?>
                           value="<?=previous_value('fee_name', $fees_detail)?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['fee_name']) ? $errors['fee_name'] : '') ?></span>
                </p>

                <p>
                    <label for="monthly_fee" 
                    class=<?=(in_array('monthly_fee', $required) ? 'required' : '') ?>
                    >Monthly Fee</label> <br/>
                    <input type="text" 
                           name="monthly_fee" 
                           id="monthly_fee" 
                           class="small_field"
                           placeholder="Enter monthly fee"
                            <?=$read_only?>
                           value="<?=previous_value('monthly_fee', $fees_detail)?>"
                    />                              
                    <span class="error">
                        <?=(!empty($errors['monthly_fee']) ? $errors['monthly_fee'] : '') ?></span>
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
