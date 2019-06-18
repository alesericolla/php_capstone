<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Include file: HEAD tag
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

?>
<html lang="en">
<head>
    <title><?=$site_name?> - <?=$page_title?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="Bella Dance School is a school of dance located in Winnipeg, Canada" />
    <meta name="keyword" content="Winipeg, dance, ballet, jazz, tap dance, belly dance, street dance, salsa, zumba" />
    
    <link rel="icon" href="images/favicon.png" type="image/gif" > 
    <link rel="apple-touch-icon" href="images/favicon.png" type="image/gif" sizes="144x144">
    <link rel="apple-touch-icon" href="images/favicon.png" type="image/gif" sizes="114x114">
    <link rel="apple-touch-icon" href="images/favicon.png" type="image/gif" sizes="72x72">
    <link rel="apple-touch-icon" href="images/favicon.png" type="image/gif" sizes="57x57">

    <meta property="og:url" content="http://salsa.uwdce.ca/~sd.alessandra/intro_php/public/index.php"/>
    <meta property="og:type" content="Website"/>
    <meta property="og:title" content="Bella Dance School"/>
    <meta property="og:description" content="Bella Dance School is a school of dance located in Winnipeg, Canada"/>
    <meta property="og:site_name" content="Bella Dance School Site"/>
    <meta property="og:image" content="http://salsa.uwdce.ca/~sd.alessandra/intro_php/public/images/favicon.png"/>
    <meta property="og:image:width" content="960"/>
    <meta property="og:image:height" content="11630"/>
    
    <!-- Link for Google Fonts: Roboto and Lato -->
    <link href="https://fonts.googleapis.com/css?family=Lato:700%7cRoboto:400,700" rel="stylesheet">
    
    <!-- Link for external CSS file  -->
    <link rel="stylesheet" 
          href="styles/capstone_project.css" 
          type="text/css"
          media="screen" 
    />   
         
    <link rel="stylesheet" 
          href="styles/print.css" 
          type="text/css"
          media="print" 
     />  
     
    <!--Conditional comments for IE browsers -->
    <!--[if LTE IE 8]>
        <link rel="stylesheet" 
              href="styles/old_ie.css" 
              type="text/css"
        />
    <![endif]-->
    
    <script src="scripts/old_ie.js"> </script>
    
    <!--Script for old versions of IE browsers -->    
    <style>
        .content p + p{
            margin: 0 auto;
            padding: 0;
        }
    </style>


    <script>
        window.onload = function() {
            <?php if ($page_title == 'Administrator') : ?>
            showGraphics();
            <?php endif ?>
            footer();
        }       
    </script>

   
    <?php if ($page_title == 'Classes') : ?>
    <!--Includes a specific style for page Classes  -->    
    
    <style>
    
        /*****************************************************/
        /* Responsive Grids                                  */
        /*****************************************************/

        .row {
            margin-top: 8px;
            margin-bottom: 8px;
            box-sizing: border-box;
        }

        /* Add padding BETWEEN each column (if you want) */
        .row,
        .row > .column {
            padding: 8px;
            margin: 0;
        }

        /* Create three equal columns that floats next to each other */
        .column {
            float: left;
            width: 50%;
            box-sizing: border-box;
        }

        /* Clear floats after rows */ 
        .row:after {
            content: "";
            display: table;
            clear: both;
            box-sizing: border-box;
        }

        /* Content */
        .content {
            padding: 20px;
            box-shadow: 0 0px 20px 0 rgba(0, 0, 0, 0.2);
            border-radius: 7px;
            box-sizing: border-box;
        }

        /* Responsive layout - makes a two column-layout instead of four columns */
        @media screen and (max-width: 900px) {
            .column {
               width: 50%;
            }
        }

        /* Responsive layout - makes the two columns stack on top of each other instead of next to each other */
        @media screen and (max-width: 600px) {
            .column {
                width: 100%;
            }
        }
          
    </style>
    <?php endif ?>

</head>
  
<!-- Change ID for Body to each Page - used for to display name of actual page on nav -->
<body id="<?php echo str_replace(' ', '', strtolower($page_title)); ?>"
    class="<?php echo (!empty($class_admin)) ? $class_admin : 'noadmin' ?>"
    >
    
    <!-- Header -->
    <header>
        <div id="background_header">
        </div>
    
        <!-- Main Logo -->
        <div id="logo">
            <div id="logo_image">
            </div>  
        </div>

        <!-- Site Navigation -->
        <nav>

            <div id="menu_group">

                <a href="#" id="menubutton">
                    <span id="topbar"></span>
                    <span id="middlebar"></span>
                    <span id="bottombar"></span>
                </a>

                <ul id="navlist" class="menu_list"> <!-- Menu list -->
                    <li><a href="index.php" class="nav_one" >Home</a></li> 
                    <li><a href="about.php" class="nav_two">About</a></li>
                    <li><a href="classes.php" class="nav_three">Classes</a></li>
                    <li><a href="timetable.php" class="nav_four">Schedules/Enroll</a></li>
                    <?php if (!empty($_SESSION['is_enrolled']) && ($_SESSION['is_enrolled'] == '1')) : ?>  
                    <li><a href="payments.php" class="nav_thirteen">Payments</a></li>  
                    <?php endif; ?>   
                    <li><a href="instructors.php" class="nav_five">Instructors</a></li>
                    <!--<li><a href="fees.php" class="nav_six">Fees</a></li>-->
                    <li><a href="contact.php" class="nav_seven">Contact Us</a></li>

                    <?php if (!empty($_SESSION['admin']) && ($_SESSION['admin'] == '1')) : ?>  
                    <li><a href="admin.php" class="nav_twelve">Admin</a></li>  
                    <?php endif; ?>     

                </ul> 
            </div>
        </nav>

        <div id="welcome">
            Welcome,
            <?php if (!empty($_SESSION['first_name'])) : ?>
                <span id="user_name"><?=$_SESSION['first_name']?>!</span>
                [
                <a class="login_logout nav_ten" href="profile.php">Profile</a>
                |
                <a class="login_logout nav_eleven" href="logout.php">Logout</a>
            <?php else : ?>
                <span id="user_name">Visitor!</span>
                [
                <a class="login_logout nav_eight" href="register.php">Register</a>
                |
                <a class="login_logout nav_nine" href="login.php">Login</a>
            <?php endif; ?>
            ]
        </div>

        <?php
        if (!empty($_SESSION['flash_msg'])) {
            $row = $_SESSION['flash_msg'];
            $class = 'flash flash-' . $row['type'];
            echo "<p class='{$class}'>{$row['message']}</p>";
            $_SESSION['flash_msg'] = [];
        }
        ?>

    </header>

