<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: index.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Home';

//Include Config File
include __DIR__ . '/../conf/config.php';

?><!doctype html>

<!-- Head -->
<?php include __DIR__ . '/../inc/head_inc.php';?> 

    <main id="page-container">
        <div id="hero_area">
            <div class="hero-image">                        
            </div>
        </div>
        
        <div id="wrapper">    
                <!--<h1 style="color:#fff;"><?=$page_title?></h1>-->
                <div class="site_description">
                    <h2 class="site_description_font">
                        Bella Dance School is located in Winnipeg,
                        Canada and has as mission to be a 
                        training center in dance and culture, 
                        promoting the balance between education, 
                        technique and health, for children, young people and adults.</h2>
                </div>

                <div id="container_age">
                    <div id="kids" class="activities_age">Kids</div>
                    <div id="adults" class="activities_age">Adults</div>            
                    <div id="youths" class="activities_age">Youths</div>    
                    <div id="professional" class="activities_age">Professional</div>    
                </div>

                <br/>

        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?>     
