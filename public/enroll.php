<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: enroll.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Schedules';

//Include Config File
include __DIR__ . '/../conf/config.php';
include __DIR__ . '/../lib/functions.php';

$action = $_POST['action'];

if (!empty($_SESSION['action']) and $_SESSION['action'] != 'Enroll') {
    if (empty($_SESSION['id_user'])) {
        $_SESSION['flash_msg'] = array(
        'type' => 'error',
        'message' => "You need to be logged in to enroll in classes!");
        header('Location: login.php');
        die();
    }

    if (empty($_POST['id_schedule'])) {
        $_SESSION['flash_msg'] = array(
        'type' => 'warning',
        'message' => "You need to be choose a classe in Schedule page before Enroll on it!");
        header('Location: timetable.php');
        die();
    }
}


if ("POST"==$_SERVER["REQUEST_METHOD"]) {
    if (!empty($_POST['action']) and $_POST['action']=='Enroll Class') {
        // create query to insert table registers (enroll in the class)
        $query = "INSERT INTO 
                  registers
                  (id_schedule,                          
                    id_student)
                 VALUES
                 (:id_schedule,                         
                    :id_student)";

        // create parameters array
        $params = array(
            ':id_schedule' => str_replace('/', '', filter_input(INPUT_POST, 'id_schedule')) ,
            ':id_student' => esc($_SESSION['id_user'])
        );

        // execute query
        execute_query($dbh, $query, $params);


        // verify if the user is not a student yet and update user table
        $query = "UPDATE users
                  set areyou = concat('student;', areyou)
                  WHERE id_user = :id_user and not exists
                        (select 'X' from students x where
                          x.id_student = :id_user )";

        // create parameters array
        $params = array(
            ':id_user' => esc($_SESSION['id_user'])
        );

        // execute query
        execute_query($dbh, $query, $params);

        // verify if the user is not a student yet and insert line in student table
        $query = "INSERT INTO students
                  (id_student, id_user)
                  select id_user, id_user from users
                  WHERE id_user = :id_user and not exists
                        (select 'X' from students x where
                          x.id_student = :id_user )";

        // create parameters array
        $params = array(
            ':id_user' => esc($_SESSION['id_user'])
        );

        // execute query
        execute_query($dbh, $query, $params);


        //After insert an enrolled in a class, updates number of enrolled students in
        //schedules table
        $query = "UPDATE schedules
                  set enrolled_students = enrolled_students + 1
                  WHERE id_schedule = :id_schedule ";

        // create parameters array
        $params = array(
            ':id_schedule' => str_replace('/', '', filter_input(INPUT_POST, 'id_schedule'))
        );
        
        // execute query
        execute_query($dbh, $query, $params);


        //After insert an enrolled in a class, updates the SESSION variable is_enrolled to '1'
        $_SESSION['is_enrolled'] = 1;

        unset($_POST['action']);
        $_SESSION['flash_msg'] = array(
        'type' => 'success',
        'message' => "Thank you for enroll on the class!");
        header('Location: timetable.php');
        die();
    }

    if (!empty($_POST['action']) and $_POST['action']=='Cancel') {
        unset($_POST['action']);
        header('Location: timetable.php');
        die();
    }
}

use classes\Schedules;

$tbschedules = new Schedules($dbh);
$schedules_detail = $tbschedules->findView(clean('id_schedule'));

?><!doctype html>
 
<!-- Head --> 
<?php include __DIR__ . '/../inc/head_inc.php';?> 
      
    <div id="wrapper">  
      
        <!-- Main Page -->
        <main> 
            
            <?php if ($action == 'Enroll') :?>
                <h1><?=$page_title?> - Enroll</h1>
                <p>Please confirm the information above, before enroll in the class:</p>
            <?php else :?>
                <h1>Schedules - Details</h1>
            <?php endif?>
                  
            <table class="no_border">   
                <tr>
                    <td style="width:60%">
                        <ul>
                            <li><strong>Class</strong>: 
                                <?=esc($schedules_detail['class_name'])?></li>
                            <li><strong>Descriptions</strong>: 
                                <?=esc($schedules_detail['class_description'])?></li>
                            <li><strong>Supplies for Class</strong>: 
                                <?=esc($schedules_detail['id_supplies'])?></li>
                            <li><strong>Supplies Description</strong>: 
                                <?=esc($schedules_detail['required_supplies'])?></li>
                            <li><strong>Days of Week</strong>: 
                                <?=(esc($schedules_detail['monday'])==1)?'Monday;':''?>
                                <?=(esc($schedules_detail['tuesday'])==1)?'Tuesday;':''?>
                                <?=(esc($schedules_detail['wednesday'])==1)?'Wednesday;':''?>
                                <?=(esc($schedules_detail['thursday'])==1)?'Thursday;':''?>
                                <?=(esc($schedules_detail['friday'])==1)?'Friday;':''?>
                                <?=(esc($schedules_detail['saturday'])==1)?'Saturday;':''?>
                                <?=(esc($schedules_detail['sunday'])==1)?'Sunday;':''?>
                            </li>
                            <li><strong>Begin Time</strong>: <?=esc($schedules_detail['begin_time'])?></li>
                            <li><strong>End Time</strong>: <?=esc($schedules_detail['end_time'])?></li>
                            <li><strong>Duration</strong>: <?=esc($schedules_detail['duration'])?></li>
                            <li><strong>Room</strong>: <?=esc($schedules_detail['room_name'])?></li>
                            <li><strong>Instructor</strong>: <?=esc($schedules_detail['instructor_name'])?></li>
                            <li><strong>Fees</strong>: 
                                <?=esc($schedules_detail['fee_name']) . '-'.
                                esc($schedules_detail['monthly_fee'])?></li>
                        </ul>
                    </td>
                    <td>
                        <?php
                        if (file_exists('images/'. $schedules_detail['image_file'])) {
                            echo "<img src='images/" . $schedules_detail['image_file'];
                            echo "' alt= ". $schedules_detail['class_name'] . " class='img_classes'>";
                        } else {
                            echo "<img src='images/default-image.png" . "' alt= ";
                            echo $schedules_detail['class_name'] . " class='img_classes img_classes_default'>";
                        }
                        ?>

                    </td>
                </tr>
            </table>

            <?php if ($action == 'Enroll') :?>
                <form action="enroll.php"
                      method="post">
                        <input type="submit" 
                               name="action"
                               value="Enroll Class"/>
                        <input type="submit" 
                               name="action"
                               value="Cancel"/>                                          
                        <input type="hidden" 
                               name="id_schedule" 
                               value=<?=$schedules_detail['id_schedule']?>/>
                </form>
            <?php else :?>
                <form action="timetable.php"
                      method="post">
                        <input type="submit" 
                               name="action"
                               value="Return"/>
                </form>
            <?php endif?>

          
        </main>
      
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
