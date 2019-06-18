<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: admin.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Administrator';
$class_admin = 'admin';

//Include Config File
include __DIR__ . '/../conf/config.php';

use classes\Ilogger;
use classes\DatabaseLogger;
use classes\FileLogger;
use classes\Dashboard;

//Verify if the user is logged as an Admin user
if (empty($_SESSION['admin']) or $_SESSION['admin'] == 0) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "You need to be logged in as an Admin user to access this page!");
    header('Location: login.php');
    die();
}

$log_result = $logger->read();

$dashboard = new Dashboard($dbh);

$dash_payments = $dashboard->allDashboard('dash_payments_vw');
$dash_payments_sum = $dashboard->allDashboard('dash_payments_sum_vw');
$dash_payments_avg = $dashboard->allDashboard('dash_payments_avg_vw');
$dash_log_page_vw = $dashboard->allDashboard('dash_log_page_vw');
$dash_enroll_vw = $dashboard->allDashboard('dash_enroll_vw');
$dash_enroll_age_vw = $dashboard->allDashboard('dash_enroll_age_vw');

$payments = [];

foreach ($dash_payments as $key => $value) {
    array_push($payments, array("y" => floatval($value['total']),"label" => $value['label'] ));
}

$payments_sum = [];

foreach ($dash_payments_sum as $key => $value) {
    array_push($payments_sum, array("y" => floatval($value['total']),"label" => $value['label'] ));
}

$payments_avg = [];

foreach ($dash_payments_avg as $key => $value) {
    array_push($payments_avg, array("y" => floatval($value['total']),"label" => $value['label'] ));
}

$visits = [];

foreach ($dash_log_page_vw as $key => $value) {
    array_push($visits, array("y" => $value['total'],"label" => $value['label'] ));
}

$enroll_month = [];

foreach ($dash_enroll_vw as $key => $value) {
    array_push($enroll_month, array("y" => $value['total'],"label" => $value['label'] ));
}


$enroll_age = [];

foreach ($dash_enroll_age_vw as $key => $value) {
    array_push($enroll_age, array("y" => $value['total'],"label" => $value['label'] ));
}

?><!doctype html>
 
<!-- Head --> 
<?php include __DIR__ . '/../inc/head_inc.php';?> 

<!-- Admin Navigation --> 
<?php include __DIR__ . '/../inc/admin_inc.php';?> 
        
    <div id="wrapper">    
        
        <!-- Main Page -->
        <main> 

            <h1>Dashboard</h1>
            <div>
                <div class="graph" id="chartContainer_pay"></div>
                <div class="graph" id="chartContainer_pay_sum" ></div>
            </div>
            <div>
                <div class="graph" id="chartContainer_pay_avg"></div>
                <div class="graph" id="chartContainer_visit" ></div>
            </div>
            <div style="margin-bottom: 750px;">
                <div class="graph" id="chartContainer_enr_month" ></div>
                <div class="graph" id="chartContainer_enr_age" ></div>
                <br/>
                <br/>
            </div>

            <h2>Log Events</h2>
            <h3>Latest log events available</h3>
            <table>    
            <?php
                echo '<tr>';
                echo '<th>Date/Time</th>';
                echo '<th>Remote Addr</th>';
                echo '<th>Request URI</th>';
                echo '<th>HTTP User Agent</th>';
                echo '<th>HTTP Response Code</th>';
                echo '</tr>';
            foreach ($log_result as $key => $event) {
                $events_ar = explode(' - ', $event);
                echo '<tr>';
                foreach ($events_ar as $key => $event_detail) {
                    //Remove field names and [] from string
                    $event_detail = str_replace('REMOTE_ADDR: ', '', $event_detail);
                    $event_detail = str_replace('REQUEST_URI: ', '', $event_detail);
                    $event_detail = str_replace('HTTP_USER_AGENT: ', '', $event_detail);
                    $event_detail = str_replace('HTTP RESPONSE CODE: ', '', $event_detail);
                    $event_detail = str_replace('[', '', $event_detail);
                    $event_detail = str_replace(']', '', $event_detail);
                             
                    echo '<td>';
                    echo $event_detail;
                    echo '</td>';
                }
                echo '</tr>';
            }
            ?>
        </table>



        </main>
        
    </div>

    <script>

        /**
         * Shows the dashboard graphics
         * @return Void
         */
        function showGraphics(){

            console.log('entrou show graphics');

            var d = new Date();
            var year = d.getFullYear();
            var month = d.getMonth();

            var month_desc =  ['January', 'February', 'March', 'April', 'May' , 'June',
                            'July', 'August', 'September', 'October', 'November' , 'December' ];

            var actual_month = month_desc[month] + '/' + year;

            var chart = new CanvasJS.Chart("chartContainer_pay", {
            animationEnabled: true,
            title:{
                text: "Payments Concluded"
            },
            /*axisY: {
                title: "Revenue (in USD)",
                prefix: "$",
                suffix:  "k"
            },*/
            data: [{
                type: "bar",
                //yValueFormatString: "$#,##0K",
                indexLabel: "{y}",
                indexLabelPlacement: "inside",
                indexLabelFontWeight: "bolder",
                indexLabelFontColor: "white",
                dataPoints: <?php echo json_encode($payments, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart.render();

        var chart_sum = new CanvasJS.Chart("chartContainer_pay_sum", {
            animationEnabled: true,
            title:{
                text: "Payments Values Total"
            },
            /*axisY: {
                title: "Revenue (in USD)",
                prefix: "$",
                suffix:  "k"
            },*/
            data: [{
                type: "bar",
                //yValueFormatString: "$#,##0K",
                indexLabel: "{y}",
                indexLabelPlacement: "inside",
                indexLabelFontWeight: "bolder",
                indexLabelFontColor: "white",
                dataPoints: <?php echo json_encode($payments_sum, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart_sum.render();

        var chart_avg = new CanvasJS.Chart("chartContainer_pay_avg", {
            animationEnabled: true,
            title:{
                text: "Payments Values Average"
            },
            /*axisY: {
                title: "Revenue (in USD)",
                prefix: "$",
                suffix:  "k"
            },*/
            data: [{
                type: "bar",
                //yValueFormatString: "$#,##0K",
                indexLabel: "{y}",
                indexLabelPlacement: "inside",
                indexLabelFontWeight: "bolder",
                indexLabelFontColor: "white",
                dataPoints: <?php echo json_encode($payments_avg, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart_avg.render();

        var chart_visits = new CanvasJS.Chart("chartContainer_visit", {
            animationEnabled: true,
            title:{
                text: "Pages Visited Today"
            },
            /*axisY: {
                title: "Revenue (in USD)",
                prefix: "$",
                suffix:  "k"
            },*/
            data: [{
                type: "bar",
                //yValueFormatString: "$#,##0K",
                indexLabel: "{y}",
                indexLabelPlacement: "inside",
                indexLabelFontWeight: "bolder",
                indexLabelFontColor: "white",
                dataPoints: <?php echo json_encode($visits, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart_visits.render();   
         

        var chart_enroll_month = new CanvasJS.Chart("chartContainer_enr_month", {
        theme: "light2",
        animationEnabled: true,
        title: {
            text: "Enrolls - " + actual_month
        },
        data: [{
            type: "pie",
            indexLabel: "{y}",
            yValueFormatString: "#,##0.00\"%\"",
            //indexLabelPlacement: "inside",
            //indexLabel: "{label} ({y})",
            indexLabelFontColor: "#36454F",
            indexLabelFontSize: 16,
            indexLabelFontWeight: "bolder",
            showInLegend: true,
            legendText: "{label}",
            dataPoints: <?php echo json_encode($enroll_month, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart_enroll_month.render();

        var chart_enroll_age = new CanvasJS.Chart("chartContainer_enr_age", {
            theme: "light2",
            animationEnabled: true,
            title: {
                text: "Enrolls by Age - " + actual_month
            },
            data: [{
                type: "pie",
                indexLabel: "{y}",
                yValueFormatString: "#,##0.00\"%\"",
                //indexLabelPlacement: "inside",
                indexLabelFontColor: "#36454F",
                indexLabelFontSize: 16,
                indexLabelFontWeight: "bolder",
                //indexLabel: "{label} ({y})",
                showInLegend: true,
                legendText: "{label}",
                dataPoints: <?php echo json_encode($enroll_age, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart_enroll_age.render();



        }
    </script>


    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
