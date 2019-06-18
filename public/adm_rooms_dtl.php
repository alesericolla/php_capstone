<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: admin_rooms_dtl.php
 * Student: Alessandra Diniz
 * Date: May/26/2019
 */

//Variables declaration
$page_title = 'Rooms - Admin';
$class_admin = 'admin';

use classes\Rooms;
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

//Verify if there is an action defined, sent by Rooms - Admin page (action)
if (empty($action)) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "This page is accessed only by Rooms - Admin page!");
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
$required = ['room_name', 'max_students'];

$id_room = filter_input(INPUT_POST, 'id_room');

$v = new Validator();

//Page called by POST in itself
if ("POST"== $_SERVER["REQUEST_METHOD"] and !empty($action_dtl) and
     $action_dtl == 'POST_ITSELF') {
    //If action_dtl is Cancel, return to previous page
    if ($action=="Cancel") {
        header('Location: adm_rooms.php');
        die();
    }

    //Verify if required fields was filled
    if ($action!="Delete") {
        $v->required($required);
        $v->validateTinyInt('max_students');
    }

    $errors = $v->errors();

    // If there is no errors, inserts the room
    if (!$errors) {
        //If action is Cancel, delete the register
        if ($action=="Delete") {
            // create query to update deleted field in rooms table
            $query = "UPDATE 
                      rooms
                      set deleted = 1, 
                      updated_at = now()
                      WHERE 
                        id_room = :id_room";

            // create parameters array
            $params = array(
                ':id_room' => filter_input(INPUT_POST, 'id_room')
            );

            $msg = "Room deleted!";
        }



        //If action is Add New, insert new register
        if ($action=="Add") {
            // create query to insert rooms table
            $query = "INSERT INTO 
                      rooms
                      (room_name, 
                      max_students)
                     VALUES
                        (:room_name, 
                        :max_students)";

            // create parameters array
            $params = array(
                ':room_name' => filter_input(INPUT_POST, 'room_name'),
                ':max_students' => filter_input(INPUT_POST, 'max_students')
            );

            $msg = "Room inserted!";
        }

        //If action is Edit New, update the register
        if ($action=="Edit") {
            // create query to update rooms table
            $query = "UPDATE 
                      rooms SET 
                      room_name = :room_name, 
                      max_students = :max_students,
                      updated_at = now()
                    WHERE 
                        id_room = :id_room";

            // create parameters array
            $params = array(
                ':id_room' => filter_input(INPUT_POST, 'id_room'),
                ':room_name' => filter_input(INPUT_POST, 'room_name'),
                ':max_students' => filter_input(INPUT_POST, 'max_students')
            );

            $msg = "Room updated!";
        }

        // execute query
        execute_query($dbh, $query, $params);

        $_SESSION['flash_msg'] = array(
            'type' => 'success',
            'message' => $msg);
        header('Location: adm_rooms.php');
        die();
    }
}

//Get rooms record details for the id selected
$tbrooms = new rooms($dbh);
$rooms_detail = $tbrooms->find($id_room);

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
                  action="adm_rooms_dtl.php"
                  autocomplete="on"
                  novalidate>

                <input type="hidden" name="csrf_token" value="<?=getToken();?>"/> 
                <span class="error">
                    <?=(!empty($errors['csrf_token']) ? $errors['csrf_token'] : '') ?></span>

                <input type="hidden" 
                       name="id_room" 
                       value="<?=previous_value('id_room', $rooms_detail)?>"/> 

                <p>
                    <label for="room_name" 
                    class=<?=(in_array('room_name', $required) ? 'required' : '') ?>
                    >Room Name</label> <br/>
                    <input type="text" 
                           name="room_name" 
                           id="room_name" 
                           class="small_field"
                           placeholder="Enter room name"
                            <?=$read_only?>
                           value="<?=previous_value('room_name', $rooms_detail)?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['room_name']) ? $errors['room_name'] : '') ?></span>
                </p>

                <p>
                    <label for="max_students" 
                    class=<?=(in_array('max_students', $required) ? 'required' : '') ?>
                    >Max of Students</label> <br/>
                    <input type="text" 
                           name="max_students" 
                           id="max_students" 
                           class="small_field"
                           placeholder="Enter max of students"
                            <?=$read_only?>
                           value="<?=previous_value('max_students', $rooms_detail)?>"
                    />                              
                    <span class="error">
                        <?=(!empty($errors['max_students']) ? $errors['max_students'] : '') ?></span>
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
