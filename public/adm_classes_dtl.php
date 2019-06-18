<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: admin_classes_dtl.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Classes - Admin';
$class_admin = 'admin';

use classes\Classes;
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

//Verify if there is an action defined, sent by Classes - Admin page (action)
if (empty($action)) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "This page is accessed only by Classes - Admin page!");
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
$required = ['class_name', 'description', 'image_file', 'id_supplies'];

$id_class = filter_input(INPUT_POST, 'id_class');

$v = new Validator();

//Page called by POST in itself
if ("POST"== $_SERVER["REQUEST_METHOD"] and !empty($action_dtl) and
     $action_dtl == 'POST_ITSELF') {
    //If action_dtl is Cancel, return to previous page
    if ($action=="Cancel") {
        header('Location: adm_classes.php');
        die();
    }

    //Verify if required fields was filled
    if ($action!="Delete") {
        $v->required($required);
    }

    $errors = $v->errors();

    // If there is no errors, inserts the classes
    if (!$errors) {
        //If action is Cancel, delete the register
        if ($action=="Delete") {
            // create query to update deleted field in classes table
            $query = "UPDATE 
                      classes
                      set deleted = 1, 
                      updated_at = now()
                      WHERE 
                        id_class = :id_class";

            // create parameters array
            $params = array(
                ':id_class' => filter_input(INPUT_POST, 'id_class')
            );

            $msg = "Class deleted!";
        }



        //If action is Add New, insert new register
        if ($action=="Add") {
            // create query to insert classes table
            $query = "INSERT INTO 
                      classes
                      (class_name, 
                      description, 
                      image_file, 
                      id_supplies)
                     VALUES
                        (:class_name, 
                        :description, 
                        :image_file, 
                        :id_supplies)";

            // create parameters array
            $params = array(
                ':class_name' => filter_input(INPUT_POST, 'class_name'),
                ':description' => filter_input(INPUT_POST, 'description'),
                ':image_file' => filter_input(INPUT_POST, 'image_file'),
                ':id_supplies' => filter_input(INPUT_POST, 'id_supplies')
            );

            $msg = "Class inserted!";
        }

        //If action is Edit New, update the register
        if ($action=="Edit") {
            // create query to update classes table
            $query = "UPDATE 
                      classes SET 
                      class_name = :class_name, 
                      description = :description, 
                      image_file = :image_file, 
                      id_supplies = :id_supplies,
                      updated_at = now()
                    WHERE 
                        id_class = :id_class";

            // create parameters array
            $params = array(
                ':id_class' => filter_input(INPUT_POST, 'id_class'),
                ':class_name' => filter_input(INPUT_POST, 'class_name'),
                ':description' => filter_input(INPUT_POST, 'description'),
                ':image_file' => filter_input(INPUT_POST, 'image_file'),
                ':id_supplies' => filter_input(INPUT_POST, 'id_supplies')
            );

            $msg = "Class updated!";
        }

        // execute query
        execute_query($dbh, $query, $params);

        $_SESSION['flash_msg'] = array(
            'type' => 'success',
            'message' => $msg);
        header('Location: adm_classes.php');
        die();
    }
}

//Get classes record details for the id selected
$tbclasses = new Classes($dbh);
$classes_detail = $tbclasses->find($id_class);

//Get clist of supplies
$tbsupplies = new Supplies($dbh);
$supplies_list = $tbsupplies->allView();

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
                  action="adm_classes_dtl.php"
                  autocomplete="on"
                  novalidate>

                <input type="hidden" name="csrf_token" value="<?=getToken();?>"/> 
                <span class="error">
                    <?=(!empty($errors['csrf_token']) ? $errors['csrf_token'] : '') ?></span>

                <input type="hidden" 
                       name="id_class" 
                       value="<?=previous_value('id_class', $classes_detail)?>"/> 

                <p>
                    <label for="class_name" 
                    class=<?=(in_array('class_name', $required) ? 'required' : '') ?>
                    >Class Name</label> <br/>
                    <input type="text" 
                           name="class_name" 
                           id="class_name" 
                           class="small_field"
                           placeholder="Enter class name"
                            <?=$read_only?>
                           value="<?=previous_value('class_name', $classes_detail)?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['class_name']) ? $errors['class_name'] : '') ?></span>
                </p>

                <p>
                    <label for="description" 
                    class=<?=(in_array('description', $required) ? 'required' : '') ?>
                    >Description</label> <br/>
                    <textarea rows="5" 
                              id="description" 
                              name="description" 
                              class="large_field"
                              placeholder="Enter description"
                              name="message"
                                <?=$read_only?>
                              ><?=previous_value('description', $classes_detail)?></textarea>
                    <span class="error">
                        <?=(!empty($errors['description']) ? $errors['description'] : '') ?></span>
                </p>


                <p>
                    <label for="image_file" 
                    class=<?=(in_array('image_file', $required) ? 'required' : '') ?>
                    >Image File Name</label> <br/>
                    <input type="text" 
                           name="image_file" 
                           id="image_file" 
                           class="medium_field"
                           placeholder="Enter the image file name"
                            <?=$read_only?>
                           value="<?=previous_value('image_file', $classes_detail)?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['image_file']) ? $errors['image_file'] : '') ?></span>
                </p>

                <p>
                    <label for="id_supplies" 
                    class=<?=(in_array('id_supplies', $required) ? 'required' : '') ?>
                    >Supplies</label> <br/>

                    <select name="id_supplies" 
                            id="id_supplies" 
                            <?=(!empty($read_only)? 'disabled' : '')?>
                            class="large_field">

                        <option value="">Select supplies</option>

                        <?php foreach ($supplies_list as $key => $row) : ?>
                        <option value=<?=$row['id_supplies']?>
                            <?=(previous_value('id_supplies', $classes_detail)==$row['id_supplies']
                             ? 'selected' : '' )?>
  
                            > <?=$row['required_supplies']?>
                        </option>
                        <?php endforeach ?>

                    </select>

                    <span class="error">
                        <?=(!empty($errors['id_supplies']) ? $errors['id_supplies'] : '') ?></span>

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
