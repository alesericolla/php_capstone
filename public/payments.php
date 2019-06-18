<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: payments_confirm.php
 * Student: Alessandra Diniz
 * Date: May/15/2019
 */

//Variables declaration
$page_title = 'Payments';

//Include Config File
include __DIR__ . '/../conf/config.php';
include __DIR__ . '/../lib/functions.php';

use classes\Payments;
use classes\Registers;
use classes\Validator;

//Define required fields
//This defition will be used to show "*" before the label
$required = ['credit_card', 'name_card', 'security_code'];

unset($_SESSION['id_payment']);

$v = new Validator();

//To execute payments is necessary to be logged in
if (empty($_SESSION['id_user'])) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "You need to be logged in to pay for the classes!");
    header('Location: login.php');
    die();
}

//Recover list of registered courses
$tbregisters = new Registers($dbh);
$registers_list = $tbregisters->findStudent(esc($_SESSION['id_user']));
$table_fields_reg = ['class_name', 'begin_time', 'end_time', 'monthly_fee'];

//Recover list of previous payments
$tbpayments = new Payments($dbh);
$payments_list = $tbpayments->findStudent(esc($_SESSION['id_user']));
$table_fields = ['payment_month', 'class_name', 'payment_value', 'payment_date'];

$year = date("Y");
$month = date("m");

//Define fields to be shown in the table of registers
$student_name = esc($_SESSION['first_name']);
$total = 0;
$qty = 0;
$automatic_payment=0;

//Get the total of monthly fee for courses witch the student are enrolled in
foreach ($registers_list as $key => $row) {
    $total = $total + $row['monthly_fee'];
    $qty++;
    $format_total= number_format($total, 2);
    $automatic_payment = $row['automatic_payment'];
}

//Verify if it is a post request
if ("POST"==$_SERVER["REQUEST_METHOD"]) {
    if (!empty($_POST['action']) and $_POST['action']=='Confirm') {
        //Verify if required fields was filled
        $v->required($required);
        
        //Validate payment fields
        $v->validatePayment();

        $errors = $v->errors();

        // If there is no errors, insert payments
        if (!$errors) {
            $dbh->beginTransaction();

            try {
                $payment_month = filter_input(INPUT_POST, 'year') . '/' .
                 filter_input(INPUT_POST, 'month');

                //Get the total of monthly fee for courses witch the student are enrolled in
                foreach ($payments_list as $key => $row) {
                    if ($payment_month==$row['payment_month']) {
                        unset($_POST['action']);
                        $_SESSION['flash_msg'] = array(
                        'type' => 'error',
                        'message' => "The year/month selected was already payed before!");
                        header('Location: payments.php');
                        die();
                    }
                }

                // create query to insert payment details table
                $query = "INSERT INTO 
                          payments_detail
                          (last_digits_card, 
                            card_name, 
                            expiry_date, 
                            total_amount)
                         VALUES
                            (:last_digits_card, 
                            :card_name, 
                            :expiry_date, 
                            :total_amount)";

                $last_digits_card = substr(filter_input(INPUT_POST, 'credit_card'), 12, 4);
                $expiry_date = filter_input(INPUT_POST, 'year_exp') . '/' .
                    filter_input(INPUT_POST, 'month_exp');

                // create parameters array
                $params = array(
                    ':last_digits_card' => $last_digits_card,
                    ':card_name' => card_name,
                    ':expiry_date' => $expiry_date,
                    ':total_amount' => $total
                );

                // execute query
                $new_id = 0;
                execute_query($dbh, $query, $params, $new_id);
                $_SESSION['id_payment'] = $new_id;

                //insert a payment for each course that the student is enrolled in
                foreach ($registers_list as $key => $row) {
                    // create query to insert payment table
                    $query = "INSERT INTO 
                              payments
                              (id_schedule,                          
                                id_student,
                                id_payment, 
                                payment_month,
                                payment_date,
                                payment_value)
                             VALUES
                             (:id_schedule,                         
                                :id_student,
                                :id_payment, 
                                :payment_month,
                                :payment_date,
                                :payment_value)";

                    $currentDateTime = date('Y-m-d H:i:s');

                    // create parameters array
                    $params = array(
                        ':id_schedule' => $row['id_schedule'],
                        ':id_student' => esc($_SESSION['id_user']),
                        ':id_payment' => $new_id,
                        ':payment_month' => $payment_month ,
                        ':payment_date' => $currentDateTime,
                        ':payment_value' => $row['monthly_fee']
                    );

                    // execute query
                    execute_query($dbh, $query, $params);
                }

                //Verify if automatic payment is checked and update student table
                $automatic_payment = filter_input(INPUT_POST, 'automatic_payment');

                if ($automatic_payment=='1') {
                    // create query to update student table
                    $query = "UPDATE students
                              set automatic_payment =1
                              WHERE         
                                id_user = :id_user ";

                    // create parameters array
                    $params = array(
                        ':id_user' => esc($_SESSION['id_user'])
                    );

                    // execute query
                    execute_query($dbh, $query, $params);
                }

                $dbh->commit();
                unset($_POST['action']);
                $_SESSION['id_payment'] = $new_id;
                $_SESSION['flash_msg'] = array(
                'type' => 'success',
                'message' => "Thank you for pay the classes!");
                header('Location: payments_confirm.php');
                die();
            } catch (Exception $e) {
                $dbh->rollback();
                die($e->getMessage());
            }
        }
    }

    if (!empty($_POST['action']) and $_POST['action']=='Cancel') {
        unset($_POST['action']);
        header('Location: timetable.php');
        die();
    }
}


//It there is no previous message, and automatic payment is autorized, show message
if (empty($_SESSION['flash_msg']) and $automatic_payment==1) {
    $_SESSION['flash_msg'] = array(
        'type' => 'warning',
        'message' => "{$student_name}, you already has automatic payments authorized! ");
}

//It there is no previous message, show total message
if (empty($_SESSION['flash_msg'])) {
    $_SESSION['flash_msg'] = array(
        'type' => 'info',
        'message' => "{$student_name}, please confirm the payment of C$ {$format_total}, 
        referent to {$qty} classes you are enrolled. ");
}

?><!doctype html>
 
<!-- Head --> 
<?php include __DIR__ . '/../inc/head_inc.php';?> 
      
    <div id="wrapper">  
      
        <!-- Main Page -->
        <main> 
                
            <h1><?=$page_title?></h1>

            <br/>
            <br/>
            <h2>Enrolled Classes</h2>

            <table>
                <tr>
                    <?php foreach ($table_fields_reg as $key => $value) : ?>
                        <th><?=format_label($value)?></th>
                    <?php endforeach ?>
                    

                </tr>

                <?php foreach ($registers_list as $key => $row) : ?>
                    <tr>
                        <?php
                        foreach ($table_fields_reg as $keylist => $value) {
                            echo '<td>'. $row[$value] . '</td>';
                        }
                        ?>

                    </tr>
                    
                <?php endforeach ?>

            </table>

            <form action="payments.php"
                  method="post"
                  autocomplete="on">

                <input type="hidden" name="csrf_token" value="<?=getToken();?>"/> 
                <span class="error">
                    <?=(!empty($errors['csrf_token']) ? $errors['csrf_token'] : '') ?></span>

                <fieldset style="width: 925px; display: block;">
                <p>                           
                    <label for="year">Payment is referent to Year/Month</label><br/> 
                    <select name="year" id="year" class="small2_field">
                        <option value="2019" <?=($year=='2019') ? 'selected' : '' ?>>2019</option>
                        <option value="2020" <?=($year=='2020') ? 'selected' : '' ?>>2020</option>
                        <option value="2021" <?=($year=='2021') ? 'selected' : '' ?>>2021</option>  
                        <option value="2022" <?=($year=='2022') ? 'selected' : '' ?>>2022</option>
                    </select>&nbsp;

                    <select name="month" id="month" class="small2_field">
                        <option value="01" <?=($month=='01') ? 'selected' : '' ?>>01</option>
                        <option value="02" <?=($month=='02') ? 'selected' : '' ?>>02</option>
                        <option value="03" <?=($month=='03') ? 'selected' : '' ?>>03</option>
                        <option value="04" <?=($month=='04') ? 'selected' : '' ?>>04</option>
                        <option value="05" <?=($month=='05') ? 'selected' : '' ?>>05</option>
                        <option value="06" <?=($month=='06') ? 'selected' : '' ?>>06</option>
                        <option value="07" <?=($month=='07') ? 'selected' : '' ?>>07</option>
                        <option value="08" <?=($month=='08') ? 'selected' : '' ?>>08</option>
                        <option value="09" <?=($month=='09') ? 'selected' : '' ?>>09</option>
                        <option value="10" <?=($month=='10') ? 'selected' : '' ?>>10</option>
                        <option value="11" <?=($month=='11') ? 'selected' : '' ?>>11</option>
                        <option value="12" <?=($month=='12') ? 'selected' : '' ?>>12</option>
                    </select>    
                    </p> 

                    <p> 
                        <label for="credit_card" 
                               class=<?=(in_array('credit_card', $required) ? 'required' : '') ?>
                               >Credit Card</label><br/> 
                        <input type="text" 
                               class="small_field"
                               name="credit_card" 
                               id="credit_card"
                               value="<?=clean('credit_card')?>"/>
                        <span class="error">
                                <?=(!empty($errors['credit_card']) ? $errors['credit_card'] : '') ?>
                        </span>                               
                    </p> 
                    <p>
                        <label for="name_card" 
                               class=<?=(in_array('name_card', $required) ? 'required' : '') ?>
                               >Cardholder's Name
                        </label><br/> 
                        <input type="text" 
                               class="small_field"
                               name="name_card" 
                               id="name_card"
                               value="<?=clean('name_card')?>"/>
                        <span class="error">
                            <?=(!empty($errors['name_card']) ? $errors['name_card'] : '') ?>
                        </span>                               
                    </p>
                    <p>
                        <label for="security_code" 
                               class=<?=(in_array('security_code', $required) ? 'required' : '') ?>
                               >Security Code
                        </label><br/> 
                        <input type="password" 
                               class="small2_field"
                               name="security_code" 
                               id="security_code"
                               value=""/>
                        <span class="error">
                                <?=(!empty($errors['security_code']) ?
                                    $errors['security_code'] : '') ?>
                        </span>                           
                    </p>
                    <p>

                        <label for="year_exp" class="required">Expiry Date</label><br/> 
                        <select name="year_exp" id="year_exp" class="small2_field">
                            <option value="" <?=(clean('year_exp')=='') ? 'selected' : '' ?>> </option>
                            <option value="2019" 
                                <?=(clean('year_exp')=='2019') ? 'selected' : '' ?>>2019</option>
                            <option value="2020" 
                                <?=(clean('year_exp')=='2020') ? 'selected' : '' ?>>2020</option>
                            <option value="2021" 
                                <?=(clean('year_exp')=='2021') ? 'selected' : '' ?>>2021</option>  
                            <option value="2022" 
                                <?=(clean('year_exp')=='2022') ? 'selected' : '' ?>>2022</option>
                        </select>&nbsp;

                        <select name="month_exp" id="month_exp" class="small2_field">
                            <option value="" 
                                <?=(clean('month_exp')=='') ? 'selected' : '' ?>> </option>
                            <option value="01" 
                                <?=(clean('month_exp')=='01') ? 'selected' : '' ?>>01</option>
                            <option value="02" 
                                <?=(clean('month_exp')=='02') ? 'selected' : '' ?>>02</option>
                            <option value="03" 
                                <?=(clean('month_exp')=='03') ? 'selected' : '' ?>>03</option>
                            <option value="04" 
                                <?=(clean('month_exp')=='04') ? 'selected' : '' ?>>04</option>
                            <option value="05" 
                                <?=(clean('month_exp')=='05') ? 'selected' : '' ?>>05</option>
                            <option value="06" 
                                <?=(clean('month_exp')=='06') ? 'selected' : '' ?>>06</option>
                            <option value="07" 
                                <?=(clean('month_exp')=='07') ? 'selected' : '' ?>>07</option>
                            <option value="08" 
                                <?=(clean('month_exp')=='08') ? 'selected' : '' ?>>08</option>
                            <option value="09" 
                                <?=(clean('month_exp')=='09') ? 'selected' : '' ?>>09</option>
                            <option value="10" 
                                <?=(clean('month_exp')=='10') ? 'selected' : '' ?>>10</option>
                            <option value="11" 
                                <?=(clean('month_exp')=='11') ? 'selected' : '' ?>>11</option>
                            <option value="12" 
                                <?=(clean('month_exp')=='12') ? 'selected' : '' ?>>12</option>
                        </select> 

                        <span class="error">
                            <?=(!empty($errors['expiration_date']) ? $errors['expiration_date'] : '') ?>
                        </span>     

                    </p> 

                    <p>
  
                        <label>
                            <input type="checkbox"
                                   id="automatic_payment"
                                   name="automatic_payment"
                                   value="1" 
                                    <?php
                                    if (!empty($_POST['automatic_payment']) && $_POST['automatic_payment']=='1') {
                                        echo 'checked';
                                    } ?>/>
                            Use this card for automatic monthly payments
                        </label> &nbsp;

                    </p>

                    <input type="submit" 
                           name="action"
                           value="Confirm"/>
                    <input type="submit" 
                           name="action"
                           value="Cancel"/>                                          
                    <input type="hidden" 
                           name="id_schedule" 
                           value=<?=esc($_SESSION['id_user'])?>
                    />
                </fieldset>
            </form>

            <br/>
            <br/>
            <h2>Previous Payments</h2>

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

        </main>
      
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
