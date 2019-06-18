<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: instructors.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Instructors';

//Include Config File
include __DIR__ . '/../conf/config.php';

use classes\Instructors;

$tbinstructors = new Instructors($dbh);
$instructors_list = $tbinstructors->allView();

$num_column = 0;

?><!doctype html>
    
<!-- Head -->
<?php include __DIR__ . '/../inc/head_inc.php';?> 
            
    <div id="wrapper">    
        <br/>
        <h1><?=$page_title?></h1>
        
        <!-- Main Page -->
        <main>  

            <?php foreach ($instructors_list as $key => $row) : ?>
            <?=($num_column == 0)? "<div class='row'>" : '' ?>             
                        
                <div class="column">
                    <div class="card">
                        <?php
                        if (!empty($row['image_file']) && file_exists('images/'. $row['image_file'])) {
                            echo "<img src='images/" . $row['image_file'];
                            echo "' alt= '". $row['instructor_name'] . "' class='photo_instructor'>";
                        } else {
                            echo "<img src='images/default-image.png'";
                            echo " alt= '". $row['instructor_name'] . "' class='photo_instructor img_classes_default'>";
                        }
                        ?>
                        <div class="container">
                            <h2><?=$row['instructor_name']?></h2>
                            <p class="title"><?=$row['title']?></p>
                            <p><?=$row['resume']?></p>
                            <p><?=$row['email']?></p>
                        </div>
                    </div>
                </div>
            <?php ($num_column == 2)? $num_column = 0 : $num_column=$num_column+1; ?> 
            <?=($num_column == 0)? "</div>" : '' ?> 

            <?php endforeach ?>
            <?=($num_column == 1)? "</div>" : '' ?> 

        </main>
        
    </div>
    
    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
