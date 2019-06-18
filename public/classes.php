<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: classes.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */
//Variables declaration
$page_title = 'Classes';

//Include Config File
include __DIR__ . '/../conf/config.php';


use classes\Classes;

$tbclasses = new Classes($dbh);
$classes_list = $tbclasses->all();

$num_column = 0;

$link = str_replace(basename($_SERVER['PHP_SELF']), 'classes', 'timetable') .'.php?search=';
 
?><!doctype html>

<!-- Head -->
<?php include __DIR__ . '/../inc/head_inc.php';?> 
    
    <div id="wrapper">  
        <!-- Main -->
        <main style="min-height:1500px;">
            <h1><?=$page_title?></h1>

            <?php foreach ($classes_list as $key => $row) : ?>
                <?=($num_column == 0)? "<div class='row'>" : '' ?>        

                <div class="column">
                    <div class="content">
                        <?php
                        if (file_exists('images/'. $row['image_file'])) {
                            echo "<img src='images/" . $row['image_file'];
                            echo "' alt= '". $row['class_name'] . "' class='img_classes'>";
                        } else {
                            echo "<img src='images/default-image.png'";
                            echo " alt= '". $row['class_name'] . "' class='img_classes img_classes_default'>";
                        }
                        ?>

                        <div class="wrap-collabsible" onclick="footer()">
                            <input id=<?='collapsible_'. str_replace(" ", "", $row['class_name'])?>
                             class="toggle" type="checkbox">
                            <label 
                             for=<?='collapsible_'. str_replace(" ", "", $row['class_name'])?> 
                             class="lbl-toggle"><?=$row['class_name']?>
                            </label>
                            <div class="collapsible-content">
                                <div class="content-inner">
                                    <p><?=$row['description']?>
                                    <br/>
                                    <a href=<?="'".
                                    str_replace(" ", "=", $link.$row['class_name']) .
                                    "'";?>>Schedules Available</a>
                                    </p>
                                </div>
                          </div>
                        </div>
                    </div>
                </div>
                
                <?php ($num_column == 1)? $num_column = 0 : $num_column=$num_column+1; ?> 
                <?=($num_column == 0)? "</div>" : '' ?> 

            <?php endforeach ?>
            <?=($num_column == 1)? "</div>" : '' ?> 
                             
        </main>
    </div>
 
    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 

