<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: timetable.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Schedules';

//Include Config File
include __DIR__ . '/../conf/config.php';
include __DIR__ . '/../lib/functions.php';

$search ='';

if (!empty(cleang('search'))) {
    $search = cleang('search');
}

$link = basename($_SERVER['PHP_SELF']) . '?';

use classes\Classes;
use classes\Schedules;
use classes\Registers;

$tbschedules = new Schedules($dbh);
$schedules_list = $tbschedules->allViewTime($search);

$tbclasses = new Classes($dbh);
$classes_list = $tbclasses->allView();

$tbregisters = new Registers($dbh);
if (!empty($_SESSION['id_user'])) {
    $classes_registered = $tbregisters->findStudent(esc($_SESSION['id_user']));
} else {
    $classes_registered = [];
}

?><!doctype html>

<!-- Head -->
<?php include __DIR__ . '/../inc/head_inc.php';?> 
            
    <div id="wrapper">    
        
        <main>

            <h1><?=$page_title?></h1>

            <table class="no_border">
                <tr>
                    <td class="align_right">
                        <form action="<?=basename($_SERVER['PHP_SELF'])?>" 
                              method="get"
                              autocomplete="on"
                              novalidate>


                                <input type="text" 
                                       name="search" 
                                       id="search"
                                       class="medium_field"
                                       value="<?=cleang('search')?>"
                                /> 
                                <button>Search</button><br/>                                                        
                        </form>
                    </td>
                </tr>
            </table>


            <div id="div_schedules">

                <div class="tabs effect-1">
                    <!-- tab-title -->
                    <input type="radio" id="tab-1" name="tab" checked="checked">
                    <a href="#tab-item-1">Monday</a>

                    <input type="radio" id="tab-2" name="tab">
                    <a href="#tab-item-2">Tuesday</a>

                    <input type="radio" id="tab-3" name="tab">
                    <a href="#tab-item-3">Wednesday</a>

                    <input type="radio" id="tab-4" name="tab">
                    <a href="#tab-item-4">Thursday</a>

                    <input type="radio" id="tab-5" name="tab">
                    <a href="#tab-item-5">Friday</a>

                    <input type="radio" id="tab-6" name="tab">
                    <a href="#tab-item-6">Saturday</a>

                    <input type="radio" id="tab-7" name="tab">
                    <a href="#tab-item-7">Sunday</a>

                    <!-- tab-content -->
                    <div class="tab-content">

                    <?php
                    for ($i=1; $i<=7; $i++) {
                        $has_schedule = false;
                        echo "<div id='tab-item-{$i}'>";

                        foreach ($schedules_list as $key => $row) {
                            $insert_line = 0;

                            if (($i==1 and $row['monday']==1) or
                                ($i==2 and $row['tuesday']==1) or
                                ($i==3 and $row['wednesday']==1) or
                                ($i==4 and $row['thursday']==1) or
                                ($i==5 and $row['friday']==1) or
                                ($i==6 and $row['saturday']==1) or
                                ($i==7 and $row['sunday']==1)) {
                                $insert_line = 1;
                                if (!$has_schedule) {
                                    echo "<table>";
                                    echo "<tr> <th>Class</th><th>Times</th><th>Duration</th>";
                                    echo "<th>Ages</th><th>Instructor</th><th></th></tr>";
                                }
                                $has_schedule = true;
                            }

                            if ($insert_line==1) {
                                echo "<tr> " .
                                     "<td>{$row['class_name']}</td>".
                                     "<td>{$row['begin_time']} - {$row['end_time']}</td>".
                                     "<td>{$row['duration']}</td>".
                                     "<td>{$row['age_range']}</td>".
                                     "<td>{$row['instructor_name']}</td>";

                                //verify if the logged user is already enrolled in the class
                                $enrolled = false;
                                foreach ($classes_registered as $key2 => $row2) {
                                    if ($row2['id_schedule']==$row['id_schedule']) {
                                        $enrolled = true;
                                        break;
                                    }
                                }
                                if (empty($_SESSION['id_user'])) {
                                        echo "<td><form action='enroll.php'
                                                  method='post'>
                                                    <input type='submit' class='size_var'
                                                           name='action'
                                                           value='Details'/>                        
                                                    <input type='hidden' 
                                                           name='id_schedule' 
                                                           value={$row['id_schedule']}/>
                                            </form></td></tr>";
                                } else {
                                    if (!$enrolled) {
                                        echo "<td><form action='enroll.php'
                                                      method='post'>
                                                        <input type='submit' class='size_var'
                                                               name='action'
                                                               value='Enroll'/>                        
                                                        <input type='hidden' 
                                                               name='id_schedule' 
                                                               value={$row['id_schedule']}/>
                                                </form></td></tr>";
                                    } else {
                                        echo "<td><form action='payments.php'
                                                      method='post'>
                                                        <input type='submit' class='size_var'
                                                               name='action'
                                                               value='Payment'/>                        
                                                        <input type='hidden' 
                                                               name='id_schedule' 
                                                               value={$row['id_schedule']}/>
                                                </form></td></tr>";
                                    }
                                }
                            }
                        }
                        if ($has_schedule==false) {
                            echo "<h2>No schedules available</h2>";
                        } else {
                            echo "</table>";
                        }

                        echo "</div>";
                    }

                    ?>
                           
                    </div>
                </div>    
            </div>


            <h2>Filter by Class</h2>
            <table class="no_border">
                <?php

                $cont = 0;

                foreach ($classes_list as $key2 => $row2) {
                    if ($cont==0) {
                        echo '<tr>';
                    }
                    echo "<td style='text-align:center'>";
                    echo "<a href='" .
                                 $link . "&search=" . str_replace(" ", "+", $row2['class_name']) . "'> ";
                    if (file_exists('images/'. $row2['image_file'])) {
                        echo "<img src='images/" . $row2['image_file'] . "' alt= '";
                        echo $row2['class_name'] . "' class='img_classes_small'>";
                    } else {
                        echo "<img src='images/default-image.png" . "' alt= '";
                        echo $row2['class_name'] . "' class='img_classes_small'>";
                    }
                    echo $row2['class_name'] . '</a>';
                    echo '</td>';
                    $cont ++;
                    if ($cont==4) {
                        echo '</tr>';
                        $cont=0;
                    }
                }

                if ($cont!=0) {
                    echo '</tr>';
                }

                ?>
                
            </table>
        </main>
    </div>    
            
    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
