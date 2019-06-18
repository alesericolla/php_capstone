<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: admin_sched.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Schedules - Admin';
$class_admin = 'admin';

use classes\Schedules;
use classes\Rooms;
use classes\Instructors;
use classes\Classes;
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

//Verify if there is an action defined, sent by Schedules - Admin page (action)
if (empty($action)) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "This page is accessed only by Schedules - Admin page!");
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
$required = ['id_class', 'begin_time', 'end_time', 'begin_date', 'end_date',
             'id_room', 'id_instructor', 'id_fee', 'age_range'];

$id_schedule = filter_input(INPUT_POST, 'id_schedule');

$v = new Validator();

//Page called by POST in itself
if ("POST"== $_SERVER["REQUEST_METHOD"] and !empty($action_dtl) and
     $action_dtl == 'POST_ITSELF') {
    //If action_dtl is Cancel, return to previous page
    if ($action=="Cancel") {
        header('Location: adm_sched.php');
        die();
    }

    //Verify if required fields was filled
    if ($action!="Delete") {
        $v->required($required);
        $v->validateDayWeek('week');
        $v->validateTime('begin_time', 'end_time');
        $v->validateDate('begin_date', 'end_date');
    }

    $errors = $v->errors();

    // If there is no errors, inserts the classes
    if (!$errors) {
        //If action is Cancel, delete the register
        if ($action=="Delete") {
            // create query to update deleted field in classes table
            $query = "UPDATE 
                      schedules
                      set deleted = 1, 
                      updated_at = now()
                      WHERE 
                        id_schedule = :id_schedule";

            // create parameters array
            $params = array(
                ':id_schedule' => filter_input(INPUT_POST, 'id_schedule')
            );

            $msg = "Schedule deleted!";
        }

        $week = filter_input(INPUT_POST, 'week', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $monday = in_array('monday', $week) == true ? 1 : 0;
        $tuesday =  in_array('tuesday', $week) == true ? 1 : 0;
        $wednesday =  in_array('wednesday', $week) == true ? 1 : 0;
        $thursday =  in_array('thursday', $week) == true ? 1 : 0;
        $friday =  in_array('friday', $week) == true ? 1 : 0;
        $saturday =  in_array('saturday', $week) == true ? 1 : 0;
        $sunday =  in_array('sunday', $week) == true ? 1 : 0;
        $duration = calc_duration_update();

        //If action is Add New, insert new register
        if ($action=="Add") {
            // create query to insert classes table
            $query = "INSERT INTO 
                      schedules
                      (id_class, monday, tuesday, wednesday, thursday, 
                       friday, saturday, sunday, begin_time, end_time, 
                       duration, begin_date, end_date, age_range, id_room, 
                       id_instructor, id_fee, enrolled_students)

                     VALUES
                      (:id_class, :monday, :tuesday, :wednesday, :thursday, 
                      :friday, :saturday, :sunday, :begin_time, :end_time, 
                      :duration, :begin_date, :end_date, :age_range, :id_room, 
                      :id_instructor, :id_fee, 0)";

            // create parameters array
            $params = array(
                ':id_class' => filter_input(INPUT_POST, 'id_class'),
                ':monday' => $monday,
                ':tuesday' => $tuesday,
                ':wednesday' => $wednesday,
                ':thursday' => $thursday,
                ':friday' => $friday,
                ':saturday' => $saturday,
                ':sunday' => $sunday,
                ':begin_time' => filter_input(INPUT_POST, 'begin_time'),
                ':end_time' => filter_input(INPUT_POST, 'end_time'),
                ':duration' => $duration,
                ':begin_date' => filter_input(INPUT_POST, 'begin_date'),
                ':end_date' => filter_input(INPUT_POST, 'end_date'),
                ':age_range' => filter_input(INPUT_POST, 'age_range'),
                ':id_room' => filter_input(INPUT_POST, 'id_room'),
                ':id_instructor' => filter_input(INPUT_POST, 'id_instructor'),
                ':id_fee' => filter_input(INPUT_POST, 'id_fee')
            );

            $msg = "Schedule inserted!";
        }

        //If action is Edit New, update the register
        if ($action=="Edit") {
            // create query to update classes table
            $query = "UPDATE 
                      schedules SET 
                      id_class = :id_class, 
                      monday = :monday, 
                      tuesday = :tuesday, 
                      wednesday = :wednesday, 
                      thursday = :thursday, 
                      friday = :friday, 
                      saturday = :saturday, 
                      sunday = :sunday, 
                      begin_time = :begin_time, 
                      end_time = :end_time, 
                      duration = :duration, 
                      begin_date = :begin_date, 
                      end_date = :end_date, 
                      age_range = :age_range, 
                      id_room = :id_room, 
                      id_instructor = :id_instructor, 
                      id_fee = :id_fee,
                      updated_at = now()
                    WHERE 
                        id_schedule = :id_schedule";

            // create parameters array
            $params = array(
                ':id_class' => filter_input(INPUT_POST, 'id_class'),
                ':monday' => $monday,
                ':tuesday' => $tuesday,
                ':wednesday' => $wednesday,
                ':thursday' => $thursday,
                ':friday' => $friday,
                ':saturday' => $saturday,
                ':sunday' => $sunday,
                ':begin_time' => filter_input(INPUT_POST, 'begin_time'),
                ':end_time' => filter_input(INPUT_POST, 'end_time'),
                ':duration' => $duration,
                ':begin_date' => filter_input(INPUT_POST, 'begin_date'),
                ':end_date' => filter_input(INPUT_POST, 'end_date'),
                ':age_range' => filter_input(INPUT_POST, 'age_range'),
                ':id_room' => filter_input(INPUT_POST, 'id_room'),
                ':id_instructor' => filter_input(INPUT_POST, 'id_instructor'),
                ':id_fee' => filter_input(INPUT_POST, 'id_fee'),
                ':id_schedule' => filter_input(INPUT_POST, 'id_schedule'),
            );
   

            $msg = "Schedule updated!";
        }

        // execute query
        execute_query($dbh, $query, $params);

        $_SESSION['flash_msg'] = array(
            'type' => 'success',
            'message' => $msg);
        header('Location: adm_sched.php');
        die();
    }
}

//Get schedules record details for the id selected
$tbschedules = new Schedules($dbh);
$schedules_detail = $tbschedules->find($id_schedule);

//If the action is not 'Add New' updates the array with days of the week
if ($action=="Add New") {
    $week = [];
    $schedules_detail['week'] = $week;
} else {
    $week = [];

    if ($schedules_detail['monday']==1) {
        $week[] = 'monday';
    }

    if ($schedules_detail['tuesday']==1) {
        $week[] = 'tuesday';
    }

    if ($schedules_detail['wednesday']==1) {
        $week[] = 'wednesday';
    }

    if ($schedules_detail['thursday']==1) {
        $week[] = 'thursday';
    }

    if ($schedules_detail['friday']==1) {
        $week[] = 'friday';
    }

    if ($schedules_detail['saturday']==1) {
        $week[] = 'saturday';
    }

    if ($schedules_detail['sunday']==1) {
        $week[] = 'sunday';
    }

    $schedules_detail['week'] = $week;
}

//Get classes list
$tbclasses = new Classes($dbh);
$classes_list = $tbclasses->all();

//Get rooms list
$tbrooms = new Rooms($dbh);
$rooms_list = $tbrooms->allView();

//Get instructors/users list
$tbinstructors = new Instructors($dbh);
$instructors_list = $tbinstructors->allView();

//Get fees list
$tbfees = new Fees($dbh);
$fees_list = $tbfees->allView();



/**
 * Calculate the duration of a class, based on begin time and end time
 * @return int Duration
 */
function calc_duration_update()
{

    $begin = new DateTime('2000-01-01 ' . filter_input(INPUT_POST, 'begin_time'));
    $end = new DateTime('2000-01-01 ' . filter_input(INPUT_POST, 'end_time'));

    //calculate interval
    $interval = date_diff($end, $begin);
    $duration = (($interval->h) * 60) +($interval->i);

    //return duration in minutes
    return $duration;
}
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
                  action="adm_sched_dtl.php"
                  autocomplete="on"
                  novalidate>

                <input type="hidden" name="csrf_token" value="<?=getToken();?>"/> 
                <span class="error">
                    <?=(!empty($errors['csrf_token']) ? $errors['csrf_token'] : '') ?></span>

                <input type="hidden" 
                       name="id_schedule" 
                       value="<?=previous_value('id_schedule', $schedules_detail)?>"/> 

                <p>
                    <label for="id_class" 
                    class=<?=(in_array('id_class', $required) ? 'required' : '') ?>
                    >Class</label> <br/>

                    <select name="id_class" 
                            id="id_class" 
                            <?=(!empty($read_only)? 'disabled' : '')?>
                            class="small_field">

                        <option value="">Select class</option>

                        <?php foreach ($classes_list as $key => $row) : ?>
                        <option value=<?=$row['id_class']?>
                            <?=(previous_value('id_class', $schedules_detail)==$row['id_class']
                             ? 'selected' : '' )?>
  
                            > <?=$row['class_name']?>
                        </option>
                        <?php endforeach ?>

                    </select>

                    <span class="error">
                        <?=(!empty($errors['id_class']) ? $errors['id_class'] : '') ?></span>

                </p>

                <fieldset style="width: 960px; display: block;">
                    <legend class="required">Days of the Week</legend>
                        
                        <label class="week_day">
                            <input type="checkbox"
                                   id="week_monday"
                                   name="week[]"
                                   value="monday" 
                                    <?php
                                    if (in_array(
                                        'monday',
                                        previous_value_array('week', $schedules_detail)
                                    )) {
                                        echo 'checked';
                                    } ?>/>
                            Monday
                        </label> &nbsp;

                        <label class="week_day">
                            <input type="checkbox"
                                   id="week_tuesday"
                                   name="week[]"
                                   value="tuesday" 
                                    <?php
                                    if (in_array(
                                        'tuesday',
                                        previous_value_array('week', $schedules_detail)
                                    )) {
                                        echo 'checked';
                                    } ?>/>
                            Tuesday
                        </label> &nbsp;

                        <label class="week_day">
                            <input type="checkbox"
                                   id="week_wednesday"
                                   name="week[]"
                                   value="wednesday" 
                                    <?php
                                    if (in_array(
                                        'wednesday',
                                        previous_value_array('week', $schedules_detail)
                                    )) {
                                        echo 'checked';
                                    } ?>/>
                            Wednesday
                        </label> &nbsp;   

                        <label class="week_day">
                            <input type="checkbox"
                                   id="week_thursday"
                                   name="week[]"
                                   value="thursday" 
                                    <?php
                                    if (in_array(
                                        'thursday',
                                        previous_value_array('week', $schedules_detail)
                                    )) {
                                        echo 'checked';
                                    } ?>/>
                            Thursday
                        </label> &nbsp;  

                        <label class="week_day">
                            <input type="checkbox"
                                   id="week_friday"
                                   name="week[]"
                                   value="friday" 
                                    <?php
                                    if (in_array(
                                        'friday',
                                        previous_value_array('week', $schedules_detail)
                                    )) {
                                        echo 'checked';
                                    } ?>/>
                            Friday
                        </label> &nbsp;  

                        <label class="week_day">
                            <input type="checkbox"
                                   id="week_saturday"
                                   name="week[]"
                                   value="saturday" 
                                    <?php
                                    if (in_array(
                                        'saturday',
                                        previous_value_array('week', $schedules_detail)
                                    )) {
                                        echo 'checked';
                                    } ?>/>
                            Saturday
                        </label> &nbsp;  

                        <label class="week_day">
                            <input type="checkbox"
                                   id="week_sunday"
                                   name="week[]"
                                   value="sunday" 
                                    <?php
                                    if (in_array(
                                        'sunday',
                                        previous_value_array('week', $schedules_detail)
                                    )) {
                                        echo 'checked';
                                    } ?>/>
                            Sunday
                        </label> <br/>
                        <span class="error" 
                        style="display:inline-block;font-weight:normal;">
                        <?=(!empty($errors['week']) ? $errors['week'] : '') ?></span>

                </fieldset>


                <p>
                    <label for="begin_time" 
                    class=<?=(in_array('begin_time', $required) ? 'required' : '') ?>
                    >Begin Time</label> <br/>
                    <input type="time" 
                           name="begin_time" 
                           id="begin_time" 
                           class="small2_field"
                            <?=$read_only?>
                           onchange="calc_duration();"
                           value="<?=previous_value('begin_time', $schedules_detail)?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['begin_time']) ? $errors['begin_time'] : '') ?></span>
                </p>  

                <p>
                    <label for="end_time" 
                    class=<?=(in_array('end_time', $required) ? 'required' : '') ?>
                    >End Time</label> <br/>
                    <input type="time" 
                           name="end_time" 
                           id="end_time" 
                           class="small2_field"
                            <?=$read_only?>
                           onchange="calc_duration();"
                           onload="calc_duration();"
                           value="<?=previous_value('end_time', $schedules_detail)?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['end_time']) ? $errors['end_time'] : '') ?></span>
                </p> 

                <p>
                    <label for="duration" 
                    class=<?=(in_array('duration', $required) ? 'required' : '') ?>
                    >Duration</label> <br/>
                    <input type="text" 
                           name="duration" 
                           id="duration" 
                           class="small2_field"
                           disabled
                           value="<?=previous_value('duration', $schedules_detail)?>"
                    />
                    minutes
                    <span class="error">
                        <?=(!empty($errors['duration']) ? $errors['duration'] : '') ?></span>
                </p>  

                <p>
                    <label for="begin_date" 
                    class=<?=(in_array('begin_date', $required) ? 'required' : '') ?>
                    >Begin Date</label> <br/>
                    <input type="date" 
                           name="begin_date" 
                           id="begin_date" 
                           class="small_field"
                            <?=$read_only?>
                           value="<?=previous_value('begin_date', $schedules_detail)?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['begin_date']) ? $errors['begin_date'] : '') ?></span>
                </p>  

                <p>
                    <label for="end_date" 
                    class=<?=(in_array('end_date', $required) ? 'required' : '') ?>
                    >End Date</label> <br/>
                    <input type="date" 
                           name="end_date" 
                           id="end_date" 
                           class="small_field"
                            <?=$read_only?>
                           value="<?=previous_value('end_date', $schedules_detail)?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['end_date']) ? $errors['end_date'] : '') ?></span>
                </p> 

                <p>
                    <label for="age_range" 
                           class=<?=(in_array('age_range', $required) ? 'required' : '') ?>
                           >Age Range</label> <br/>
                    <select name="age_range" id="age_range" class="small_field">
                        <option value="">Select a age range</option>
                        <option value="3-6" 
                            <?=(previous_value(
                                'age_range',
                                $schedules_detail
                            )=='3-6') ? 'selected' : '' ?>>3-6</option>
                        <option value="7-8" 
                            <?=(previous_value(
                                'age_range',
                                $schedules_detail
                            )=='7-8') ? 'selected' : '' ?>>7-8</option>
                        <option value="9-12" 
                            <?=(previous_value(
                                'age_range',
                                $schedules_detail
                            )=='9-12') ? 'selected' : '' ?>>9-12</option>
                        <option value="13-17" 
                            <?=(previous_value(
                                'age_range',
                                $schedules_detail
                            )=='13-17') ? 'selected' : '' ?>>13-17</option>
                        <option value="18+" 
                            <?=(previous_value(
                                'age_range',
                                $schedules_detail
                            )=='18+') ? 'selected' : '' ?>>18+</option>
                        <option value="all" 
                            <?=(previous_value(
                                'age_range',
                                $schedules_detail
                            )=='all') ? 'selected' : '' ?>>all</option>

                    </select>
                    <span class="error"><?=(!empty($errors['age_range']) ? $errors['age_range'] : '') ?></span>
                <p>                                                 

                <p>
                    <label for="id_instructor" 
                    class=<?=(in_array('id_instructor', $required) ? 'required' : '') ?>
                    >Instructor</label> <br/>

                    <select name="id_instructor" 
                            id="id_instructor" 
                            <?=(!empty($read_only)? 'disabled' : '')?>
                            class="small_field">

                        <option value="">Select instructor</option>

                        <?php foreach ($instructors_list as $key => $row) : ?>
                        <option value=<?=$row['id_instructor']?>
                            <?=(previous_value('id_instructor', $schedules_detail)==
                              $row['id_instructor']
                             ? 'selected' : '' )?>
  
                            > <?=$row['first_name'] . ' ' . $row['last_name']?>
                        </option>
                        <?php endforeach ?>

                    </select>

                    <span class="error">
                        <?=(!empty($errors['id_instructor']) ? $errors['id_instructor'] : '') ?></span>

                </p>  

                <p>
                    <label for="id_room" 
                    class=<?=(in_array('id_room', $required) ? 'required' : '') ?>
                    >Room</label> <br/>

                    <select name="id_room" 
                            id="id_room" 
                            <?=(!empty($read_only)? 'disabled' : '')?>
                            class="small2_field">

                        <option value="">Select room</option>

                        <?php foreach ($rooms_list as $key => $row) : ?>
                        <option value=<?=$row['id_room']?>
                            <?=(previous_value('id_room', $schedules_detail)==$row['id_room']
                             ? 'selected' : '' )?>
  
                            > <?=$row['room_name']?>
                        </option>
                        <?php endforeach ?>

                    </select>

                    <span class="error">
                        <?=(!empty($errors['id_room']) ? $errors['id_room'] : '') ?></span>

                </p>  

                <p>
                    <label for="id_fee" 
                    class=<?=(in_array('id_fee', $required) ? 'required' : '') ?>
                    >Fee</label> <br/>

                    <select name="id_fee" 
                            id="id_fee" 
                            <?=(!empty($read_only)? 'disabled' : '')?>
                            class="small_field">

                        <option value="">Select fee</option>

                        <?php foreach ($fees_list as $key => $row) : ?>
                        <option value=<?=$row['id_fee']?>
                            <?=(previous_value('id_fee', $schedules_detail)==$row['id_fee']
                             ? 'selected' : '' )?>
  
                            > <?=$row['fee_name']?>
                        </option>
                        <?php endforeach ?>

                    </select>

                    <span class="error">
                        <?=(!empty($errors['id_fee']) ? $errors['id_fee'] : '') ?></span>

                </p>   

                <p>
                    <label for="enrolled_students" 
                    class=<?=(in_array('enrolled_students', $required) ? 'required' : '') ?>
                    >Students Enrolled</label> <br/>
                    <input type="text" 
                           name="num_enrolled_students" 
                           id="num_enrolled_students" 
                           class="small2_field"
                           disabled
                           value="<?=(previous_value('enrolled_students', $schedules_detail)=='' ?
                            0 : previous_value('enrolled_students', $schedules_detail))?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['enrolled_students']) ? $errors['enrolled_students'] : '') ?></span>
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

    <script>

        //Create event load for the page, to update duration in minutes
        window.addEventListener("load", calc_duration);


        /**
         * Calculate the duration of a class, based on begin time and end time
         * @return Void
         */
        function calc_duration(){

            var begin = new Date('01/01/2000 ' + document.getElementById("begin_time").value);
            var end = new Date('01/01/2000 ' + document.getElementById("end_time").value);

            //calculate duration in minutes
            var duration = (end - begin) / 1000 / 60;

            //Verify if duration is negative, then moves 0 to duration
            if(duration<0){
                duration = 0;
            }
            document.getElementById("duration").value = duration;
            return;
        }

    </script>

    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
