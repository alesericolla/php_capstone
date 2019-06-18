<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: payments.php
 * Student: Alessandra Diniz
 * Date: May/14/2019
 */

//Variables declaration
$page_title = 'Payments';

//Include Config File
include __DIR__ . '/../conf/config.php';
include __DIR__ . '/../lib/functions.php';

use classes\Payments;

//To execute payments is necessary to be logged in
if (empty($_SESSION['id_user'])) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "You need to be logged in to pay for the classes!");
    header('Location: login.php');
    die();
}

//To see detail of payments is necessary to become from payments page
if (empty($_SESSION['id_payment'])) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "You can see details of a payment only when you finish the payment!");
    header('Location: payments.php');
    die();
}

//Recover list of previous payments
$tbpayments = new Payments($dbh);
$payments_list = $tbpayments->findStudent(esc($_SESSION['id_user']), esc($_SESSION['id_payment']));
$table_fields = ['payment_month', 'class_name', 'payment_value', 'payment_date'];


$payments_detail = $tbpayments->findPaymentDetail(esc($_SESSION['id_payment']));

?><!doctype html>
 
<!-- Head --> 
<?php include __DIR__ . '/../inc/head_inc.php';?> 
      
    <div id="wrapper">  
      
        <!-- Main Page -->
        <main> 
                
            <h1><?=$page_title?></h1>

            <br/>
            <br/>
            <h2>Thank you for you payment!</h2>

            <ul>

            <?php foreach ($payments_detail as $key => $row) : ?>
                <li><strong>Credit Card Last 4 Digits</strong>: <?=esc($row['last_digits_card'])?></li>
                <li><strong>Card Name</strong>: <?=esc($row['card_name'])?></li>
                <li><strong>Expiry Date</strong>: <?=esc($row['expiry_date'])?></li>
                <li><strong>Total Amount</strong>: <?=esc($row['total_amount'])?></li>
            <?php endforeach ?>

            </ul>

            <table>
                <tr>
                    <?php foreach ($table_fields as $key => $value) : ?>
                        <th><?=format_label($value)?></th>
                    <?php endforeach ?>
                    

                </tr>

                <?php foreach ($payments_list as $key => $row) : ?>
                    <tr>
                        <?php
                        foreach ($table_fields as $keylist => $value) {
                            echo '<td>'. $row[$value] . '</td>';
                        }
                        ?>

                    </tr>
                    
                <?php endforeach ?>

            </table>

            <p>
                <form action="payments.php"
                      method="post">
                        <input type="submit" 
                               name="action"
                               value="Return"/>
                </form>
            </p>
        </main>
      
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
