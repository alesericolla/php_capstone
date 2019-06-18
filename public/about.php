<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: about.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

$page_title = 'About';

//Include Config File
include __DIR__ . '/../conf/config.php';

?><!doctype html>
<!-- Head -->
<?php include __DIR__ . '/../inc/head_inc.php';?> 
   
    <div id="wrapper">  
      
        <!-- Main Page -->
        <main> 

            <div>
                <!--About Information-->
                <h1><?=$page_title?></h1>
                <p>Bella Dance School was founded by Isabella Soares in 2008, 
                    with the vision of establishing the highest quality standard in dance.
                </p>
                <p>We act with the formation of various modalities, 
                    such as Ballet, Jazz, Tap Dance, Belly Dance, Street Dance, Salsa, Zumba among others.
                </p>
                <p>We strive for excellence in technical training, 
                    fundamental to the mission of the school. 
                    The school has highly qualified teachers, all with specialization courses.
                </p>
                <p>Our work is closely linked to physical development and for 
                    this we count on a multidisciplinary team to guide our students
                     in all their needs, with Physiotherapists, Psychologists and Nutritionists.
                </p>
                <p>The Bella Dance School has a history replete with achievements 
                    over the years. They are festivals of dances, championships 
                    and presentations by all the Canada. These achievements reflect 
                    the effort and dedication of a passionate work.
                </p>
                <!--Structure Information-->
                <h2>Structure</h2>
                <p>With more than 600 m² of built area, Bella Dance School has:
                </p>
                <ul>
                    <li>4 large rooms</li>
                    <li>Video Library and Library</li>
                    <li>Women's and Men's Dressing Rooms</li>
                    <li>Store with dance items</li>
                    <li>Child's space</li>
                    <li>Kitchen and pantry</li>
                    <li>Parking</li>
                    <li>Wi-Fi</li>
                </ul>

            </div>   
            
            <br/>
            <!--Location Information-->
            <h2>Location</h2>
            <br/>
            <address>
              460 Portage Ave -  R99 99R <br/>
              Winnipeg - MB <br/>
              (204) 333-4444 <br/>
              contact@bella-dance.ca 
            </address>
            <br/>
            
            <img src="images/google_map.jpg" alt="Google Maps" style="width:100%;margin-bottom:30px;">

            <!--Open Hours Information-->
            <h2>Open Hours</h2>
            <br/>
            <table id="open_hours" >
                <tr>
                    <td>Monday/Wednesday</td>
                    <td>2:00pm - 8:00pm</td>
                </tr>
                <tr>
                    <td>Tuesday/Thursday</td>
                    <td>4:00pm - 9:00pm</td>
                </tr>
                <tr>
                    <td>Friday</td>
                    <td>2:00pm - 8:00pm</td>
                </tr>
                <tr>
                    <td>Saturday</td>
                    <td>9:00pm - 2:00pm</td>
                </tr>
            </table>

            <br/>
            <br/>
            <p>Through this philosophy we seek to advance the development 
            of art in order to enrich the cultural life of our community.</p>
            <p>Start dance with us!</p>
        </main>

    </div>

    <!-- Footer -->    
    <?php include __DIR__ . '/../inc/footer_inc.php';?>   
