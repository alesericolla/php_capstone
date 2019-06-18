<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: Validator
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */
namespace classes;

class Validator
{

    private $errors=[];

    /**
     * Verify if fields defined as required, has some value
     * @param Array $field_list - required field list
     * @return void
     */
    public function required($field_list)
    {

       //Verify if required fields are empty
        foreach ($field_list as $key => $value) {
            if (!filter_input(INPUT_POST, $value)) {
                $this->setError($value, format_label($value) . ' is a required field!');
            }
        }
    }


    /**
     * Verify if the field form is a valid email
     * @param String $field - field name to be validate if it is a valid email
     * @return void
     */
    public function validateEmail($field)
    {

        //Verify if it is a valid email
        if (!filter_input(INPUT_POST, $field, FILTER_VALIDATE_EMAIL)) {
            $this->setError($field, 'This is not a valid email!');
        }
    }

    /**
     * Verify if the email already exists in users table
     * @param String $field - field name to be validate if it is a valid email
     * @return void
     */
    public function validateUserEmail($field, $dbh)
    {

        //Verify if there is no errors for the email field
        if (empty($this->errors['email'])) {
            try {
                // create query for users table
                $query = "SELECT id_user
                          FROM users
                          WHERE email = :email";

                $params = array(
                  ':email' => filter_input(INPUT_POST, 'email')
                );

                // prepare query
                $stmt = $dbh->prepare($query);

                // execute query
                $stmt->execute($params);

                $result = $stmt->fetch(\PDO::FETCH_ASSOC);

                $msg="This email is already registered! Please go to <a href='login.php'>Login</a> page.";
                if (!empty($result)) {
                    $_SESSION['flash_msg'] = array(
                      'type' => 'error',
                      'message' => $msg);

                    $this->setError($field, "This email is already registered!");
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }
    }

    /**
     * Verify if the field form is a valid phone
     * @param String $field - field name to be validate if it is a valid phone
     * @return void
     */
    public function validatePhone($field)
    {

        //Validate phone format
        $pattern_phone = '/^((\(([0-9]{3})\)[\-\.\s]?)|([0-9]{3}[\-\.\s]{1}))([0-9]{3})([\-\.\s]{1})([0-9]{4})$/';

        $phone = filter_input(INPUT_POST, $field);

        if (!empty($phone) && !preg_match($pattern_phone, $phone)) {
          // If the phone is in a invalid format
          // The formats accepted are (999)999-9999 or 999-999-9999 or 999 999 9999
            $this->setError(
                $field,
                'Please enter a valid Phone Number. It accepts (999)999-9999 or 999-999-9999 or 999 999 9999!'
            );
        }
    }

    /**
     * Verify if the field form is a valid postal code
     * @param String $field - field name to be validate if it is a valid postal code
     * @return void
     */
    public function validatePostalCode($field)
    {

        //Validate phone format
        $pattern = '/^[A-Z][0-9][A-Z][0-9][A-Z][0-9]$/';

        $postal_code = filter_input(INPUT_POST, $field);

        if (!empty($postal_code) && !preg_match($pattern, $postal_code)) {
      // If the postal code is in a invalid format
      // The formats accepted is "A9A9A9"
            $this->setError($field, "Please enter a valid Postal Code. It must have the following format: 'A9A9A9'.");
        }
    }

    /**
     * Verify if the field form is a valid birthday
     * @param String $field - field name to be validate if it is a valid birthday
     * @return void
     */
    public function validateBirthday($field)
    {

        $birthday = filter_input(INPUT_POST, $field);
        
        //Verify if the birthday is filled and if it is a date
        //This validation was done because some browsers do not have DATE input type
        if (!empty($birthday)) {
            $dt = new \DateTime($birthday);
            $month = $dt->format('m');
            $day = $dt->format('d');
            $year = $dt->format('Y');
            if (!checkdate($month, $day, $year)) {
                $this->setError($field, 'This is not a valid date!');
            } else {
                $today = new \DateTime();
              //Verify if birthdate is bigger or equal than today
                if ($dt>=$today) {
                    $this->setError($field, 'Birthday must be less than today!');
                }
            }
        }
    }

    /**
     * Verify if the password and confirm password are equal
     * @param String $field         - password field name to be validate
     * @param String $field_confirm - confirm password field name to be validate
     * @return void
     */
    public function validatePassword($field, $field_confirm)
    {

        $pass = filter_input(INPUT_POST, $field);
        $pass_conf = filter_input(INPUT_POST, $field_confirm);

        //Verify if the passempty($pass)word and confirmation password are filled and if they are equal
        if (isset($pass) && isset($pass_conf)) {
            if ($pass != $pass_conf) {
                $this->setError($field, 'Password and Confirm Password are different!');
            }
        }
    }

    
    /**
     * Verify if the field form has at least one option for user type, and its respectives fields,
     * according to the type, were filled
     * @param String $field - field name to be validate if it has at least one option for user type checked
     * @return void
     */
    public function validateUserType($field)
    {

        $user_type = filter_input(INPUT_POST, $field, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

       //Verify if at least one type of user was clicked
        if (empty($user_type)) {
            $this->setError($field, 'Choose at least one option!');
        } else {
          //Verify if type of user is student
            if (in_array('student', $user_type)) {
                $dt = new \DateTime(filter_input(INPUT_POST, 'birthday'));
                $today = new \DateTime();
                $diff = date_diff($dt, $today);

                //Calculate difference in years
                $age = $diff->y;

                //If age is less than 18, it is necessary to inform Parent/Guardian name and phone
                if ($age<18) {
                    if (empty(filter_input(INPUT_POST, 'parent_guardian'))) {
                        $this->setError(
                            'parent_guardian',
                            'Parent/guardian name is a required field for age less than 18!'
                        );
                    }
                    if (empty(filter_input(INPUT_POST, 'parent_guardian_phone'))) {
                        $this->setError(
                            'parent_guardian_phone',
                            'Parent/guardian phone is a required field for age less than 18!'
                        );
                    } else {
                        $pattern_phone =
                        '/^((\(([0-9]{3})\)[\-\.\s]?)|([0-9]{3}[\-\.\s]{1}))([0-9]{3})([\-\.\s]{1})([0-9]{4})$/';
                      //Validate phone format for parent/guardian
                        if (!preg_match($pattern_phone, filter_input(INPUT_POST, 'parent_guardian_phone'))) {
                          // If the phone is in a invalid format
                          // The formats accepted are (999)999-9999 or 999-999-9999 or 999 999 9999
                            $msg ='Please enter a valid Phone Number. ';
                            $msg = $msg . 'It accepts (999)999-9999 or 999-999-9999 or 999 999 9999!';
                            $this->setError(
                                'parent_guardian_phone',
                                $msg
                            );
                        }
                    }
                }
            }

          //Verify if type of user is instructor
            if (in_array('instructor', $user_type)) {
              //If age is instructor, it is necessary to inform the resume
                if (empty(filter_input(INPUT_POST, 'resume'))) {
                    $this->setError('resume', 'Resume is a required field for instructors!');
                }
            }
        }
    }


    /**
     * Verify if the at least one sday of the week was checked for schedules
     * @return void
     */
    public function validateDayWeek($field)
    {

        $days_week = filter_input(INPUT_POST, $field, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

       //Verify if at least one day of the week was clicked
        if (empty($days_week)) {
            $this->setError($field, 'Choose at least one day of the week for the schedule!');
        }
    }

    /**
     * Verify if the end time is greater than begin time
     * @return void
     */
    public function validateTime($field_begin, $field_end)
    {

        $begin = filter_input(INPUT_POST, $field_begin);
        $end = filter_input(INPUT_POST, $field_end);

        if (!empty($begin) and !empty($end) and ($begin>=$end)) {
            $this->setError($field_end, 'End Time must be greater than Begin Time!');
        }
    }

    /**
     * Verify if the end date is greater than begin date
     * @return void
     */
    public function validateDate($field_begin, $field_end)
    {

        $begin = filter_input(INPUT_POST, $field_begin);
        $end = filter_input(INPUT_POST, $field_end);

        if (!empty($begin) and !empty($end) and ($begin>=$end)) {
            $this->setError($field_end, 'End Date must be greater than Begin Date!');
        }
    }

    /**
     * Verify if the email and password are valid to login
     * @param String $field_email -  email field name to be validate to login
     * @param String $field_pass  -  password field name to be validate to login
     * @return void
     */
    public function validateLogin($email_field, $field_pass, $dbh)
    {

        $email = filter_input(INPUT_POST, $email_field);
        $pass = filter_input(INPUT_POST, $field_pass);

        if (!empty($email) && !empty($pass) && empty($this->errors)) {
            try {
                // create query for users table
                $query = "SELECT id_user, first_name, password, admin,
                                 ifnull((select distinct '1' from registers 
                                 where id_student = id_user ),0) as is_enrolled
                          FROM users
                          WHERE email = :email";

                $params = array(
                  ':email' => filter_input(INPUT_POST, 'email')
                );

                // prepare query
                $stmt = $dbh->prepare($query);

                // execute query
                $stmt->execute($params);

                $result = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!empty($result)) {
                    if (password_verify($pass, $result['password'])) {
                        $_SESSION['id_user'] = $result['id_user'];
                        $_SESSION['first_name'] = $result['first_name'];
                        $_SESSION['admin'] = $result['admin'];
                        $_SESSION['is_enrolled'] = $result['is_enrolled'];
                        return;
                    }
                }

                  $this->setError('email', 'There is a problem with your credentials! Please try again!');
                  return false;
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }
    }

    /**
     * Verify if payment fields are valids
     * @return void
     */
    public function validatePayment()
    {

        //Validate credit card
        $pattern_card = '/^([0-9]{16})$/';

        $credit_card = filter_input(INPUT_POST, 'credit_card');

        // If the card number is in a invalid format
        if (!empty($credit_card) && !preg_match($pattern_card, $credit_card)) {
            $this->setError('credit_card', 'Please enter a valid Credit Card!');
        }

        //Validate security code
        $pattern_code = '/^([0-9]{3})$/';

        $security_code = filter_input(INPUT_POST, 'security_code');

        // If the security code is in a invalid format
        if (!empty($security_code) && !preg_match($pattern_code, $security_code)) {
            $this->setError('security_code ', 'Please enter a valid Security Code!');
        }

        //Validate name
        $pattern_name = '/^([A-Za-z\s]{2,20})$/';

        $name_card = filter_input(INPUT_POST, 'name_card');

        // If the security code is in a invalid format
        if (!empty($name_card) && !preg_match($pattern_name, $name_card)) {
            $this->setError('name_card', 'Please enter a valid Name on Card!');
        }
    
        //Validate expiry date
        $year_exp = filter_input(INPUT_POST, 'year_exp');
        $month_exp = filter_input(INPUT_POST, 'month_exp');

        // If the security code is in a invalid format
        if (empty($year_exp) or empty($month_exp)) {
            $this->setError('expiration_date', 'Please select a valid Expiry Date!');
        }
    }

    /**
     * Verify if the field form is a decimal number
     * @param String $field - field name to be validate if it is a decimal number
     * @return void
     */
    public function validateDecimal($field)
    {

        //Validate decimal number
        $pattern = '/[0-9.]+/';

        $decimal_number = filter_input(INPUT_POST, $field);

        if (!empty($decimal_number) && !preg_match($pattern, $decimal_number)) {
      // If the decimal number is in a invalid format
            $this->setError($field, "Please enter a valid decimal number. 
                It accepts only numbers and decimal character.");
        }
    }
    
    /**
     * Verify if the field form is a integer number
     * @param String $field - field name to be validate if it is a integer number
     * @return void
     */
    public function validateInteger($field)
    {

        //Validate decimal number
        $pattern = '/[0-9]+/';

        $int_number = filter_input(INPUT_POST, $field);

        if (!empty($int_number) && !preg_match($pattern, $int_number)) {
      // If the integer number is in a invalid format
            $this->setError($field, "Please enter a valid integer number. It accepts only numbers.");
        }
    }

    /**
     * Verify if the field form is a tiny integer number
     * @param String $field - field name to be validate if it is a tiny integer number
     * @return void
     */
    public function validateTinyInt($field)
    {

        //Validate decimal number
        $pattern = '/[0-9]{1,3}/';

        $int_number = filter_input(INPUT_POST, $field);

        if (!empty($int_number) && !preg_match($pattern, $int_number)) {
           // If the tiny integer number is in a invalid format
            $this->setError($field, "Please enter a valid integer number. It accepts only numbers.");
        } else {
            // If the number is in greater than 127
            if ($int_number > 127) {
                $this->setError($field, "Please enter a valid tiny integer number. 
                    It accepts values less than 128.");
            }
        }
    }

    /**
     * Verify if the field id supplies is valid
     * @param String $field - field name to be validate
     * @return void
     */
    public function validateIdSupplies($field)
    {

        //Validate if it has 6 any characters
        $pattern = '/^[0-9A-Za-z]{6}$/';

        $value = filter_input(INPUT_POST, $field);

        if (!empty($value) && !preg_match($pattern, $value)) {
           // If the decimal number is in a invalid format
            $this->setError($field, "Please enter a valid Id Supplies. 
                Id Supplies must have 6 characters, it accepts numbers and letters.");
        }
    }

    /**
     * Return the errors array
     * @return Array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Set error message if a message has not already been set for a field
     * @param String $field the field to set the error for
     * @param String $message the message
     * @return void
     */
    private function setError($field, $message)
    {
        if (empty($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }
}
