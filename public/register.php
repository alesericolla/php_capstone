<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: register.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Register';

//Define required fields
//This defition will be used to show "*" before the label
$required = ['first_name', 'last_name', 'email', 'phone', 'street', 'city',
             'province','country', 'postal_code', 'birthday',
             'password', 'confirm_password'];

//Include Config File
include __DIR__ . '/../conf/config.php';
include __DIR__ . '/../lib/functions.php';

//Required Validator File and Functions File

use classes\Validator;

$v = new Validator();

//Verify if it is a POST request,
if ('POST' == $_SERVER['REQUEST_METHOD']) {
    //Verify if required was filled
    $v->required($required);

    $v->validateEmail('email');

    $v->validateUserEmail('email', $dbh);

    $v->validatePhone('phone');

    $v->validatePostalCode('postal_code');

    $v->validateBirthday('birthday');

    $v->validatePassword('password', 'confirm_password');

    $v->validateUserType('areyou');

    $errors = $v->errors();

    // If there is no errors, inserts the user
    if (!$errors) {
        $dbh->beginTransaction();

        try {
            // create query for users table
            $query = "INSERT INTO 
                      users
                      (first_name,                         
                        last_name,
                        email,
                        phone,
                        street,
                        city,
                        province,
                        country,
                        postal_code,
                        birthday,
                        password,
                        areyou, 
                        active_status)
                     VALUES
                     (:first_name,                         
                        :last_name,
                        :email,
                        :phone,
                        :street,
                        :city,
                        :province,
                        :country,
                        :postal_code,
                        :birthday,
                        :password,
                        :areyou, 
                        1)";
            
            $password_encrypted = encryptPass('password');
            
            $areyou_array = filter_input(INPUT_POST, 'areyou', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $areyou_string = implode(';', $areyou_array);

            $params = array(
                ':first_name' => filter_input(INPUT_POST, 'first_name') ,
                ':last_name' => filter_input(INPUT_POST, 'last_name'),
                ':email' => filter_input(INPUT_POST, 'email'),
                ':phone' => filter_input(INPUT_POST, 'phone'),
                ':street' => filter_input(INPUT_POST, 'street'),
                ':city' => filter_input(INPUT_POST, 'city'),
                ':province' => filter_input(INPUT_POST, 'province'),
                ':country' => filter_input(INPUT_POST, 'country'),
                ':postal_code' => filter_input(INPUT_POST, 'postal_code'),
                ':birthday' => filter_input(INPUT_POST, 'birthday'),
                ':areyou' => $areyou_string,
                ':password' => $password_encrypted
            );

            //Call function execute_query
            $new_id=0;
            if (execute_query($dbh, $query, $params, $new_id)) {
                //If it is a student, insert data in student table
                if (in_array('student', $areyou_array)) {
                    $query = "INSERT INTO 
                              students
                                (id_student,
                                id_user,
                                parent_guardian,
                                parent_guardian_phone)
                              values
                                (:id_student,
                                :id_user,
                                :parent_guardian,
                                :parent_guardian_phone)";
                    $params = array(
                        ':id_student' => $new_id,
                        ':id_user' => $new_id,
                        ':parent_guardian' =>
                            (!empty(filter_input(INPUT_POST, 'parent_guardian')) ?
                             filter_input(INPUT_POST, 'parent_guardian') : null),
                        ':parent_guardian_phone' =>
                            (!empty(filter_input(INPUT_POST, 'parent_guardian_phone')) ?
                             filter_input(INPUT_POST, 'parent_guardian_phone') : null)
                    );

                    execute_query($dbh, $query, $params);
                }

                //If it is an instructor, insert data in instructor table
                if (in_array('instructor', $areyou_array)) {
                    $query = "INSERT INTO 
                              instructors
                                (id_instructor,
                                id_user,
                                resume)
                              values
                                (:id_instructor,
                                :id_user,
                                :resume)";
                    $params = array(
                        ':id_instructor' => $new_id,
                        ':id_user' => $new_id,
                        ':resume' => filter_input(INPUT_POST, 'resume')
                    );

                    execute_query($dbh, $query, $params);
                }

                $dbh->commit();

                //Regenerate new session id when user registered
                session_regenerate_id();

                $_SESSION['id_user'] = $new_id;
                $_SESSION['first_name'] = filter_input(INPUT_POST, 'first_name');

                $_SESSION['flash_msg'] = array(
                    'type' => 'success',
                    'message' => "You have successfuly registered!");
                header('Location: profile.php');
                die();
            }
        } catch (Exception $e) {
            $dbh->rollback();
            die($e->getMessage());
        }
    } else {
        unset($_POST['password']);
        unset($_POST['confirm_password']);
    }
}
    
?><!doctype html>

<!-- Head -->
<?php include __DIR__ . '/../inc/head_inc.php';?> 
            
    <div id="wrapper">    
        
        <!-- Main Page -->
        <main> 
            <form method="post"
                  action="register.php"
                  autocomplete="on"
                  novalidate>
            
                <h1 class="form_title"><?=$page_title?></h1>

                <input type="hidden" name="csrf_token" value="<?=getToken();?>"/> 
                <span class="error">
                    <?=(!empty($errors['csrf_token']) ? $errors['csrf_token'] : '') ?></span>

                <p>
                    <label for="first_name" 
                    class=<?=(in_array('first_name', $required) ? 'required' : '') ?>
                    >First Name</label> <br/>
                    <input type="text" 
                           name="first_name" 
                           id="first_name" 
                           class="small_field"
                           placeholder="Enter your first name"
                           value="<?=clean('first_name')?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['first_name']) ? $errors['first_name'] : '') ?></span>
                </p>

                <p>
                    <label for="last_name" 
                          class=<?=(in_array('last_name', $required) ? 'required' : '') ?>
                          >Last Name
                    </label> <br/>
                    <input type="text" 
                           name="last_name" 
                           id="last_name" 
                           class="small_field"
                           placeholder="Enter your last name"
                           value="<?=clean('last_name')?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['last_name']) ? $errors['last_name'] : '') ?></span>
                </p>

                <p>
                    <label for="email" 
                    class=<?=(in_array('email', $required) ? 'required' : '') ?>
                    >Email Address</label> <br/>
                    <input type="email" 
                           name="email" 
                           id="email"
                           class="medium_field"
                           placeholder="Enter your email"
                           value="<?=clean('email')?>"
                    />
                    <span class="error"><?=(!empty($errors['email']) ? $errors['email'] : '') ?></span>
                </p>

                <p>
                    <label for="phone" 
                        class=<?=(in_array('phone', $required) ? 'required' : '') ?>
                        >Phone Number
                    </label> <br/>
                    <input type="text" 
                           name="phone" 
                           id="phone" 
                           class="small_field"
                           placeholder="Enter your phone number"
                           value="<?=clean('phone')?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['phone']) ? $errors['phone'] : '') ?></span>
                </p>

                <p>
                    <label for="street" 
                        class=<?=(in_array('street', $required) ? 'required' : '') ?>
                        >Street</label> <br/>
                    <input type="text" 
                           name="street" 
                           id="street"
                           class="medium_field"
                           placeholder="Enter your street"
                           value="<?=clean('street')?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['street']) ? $errors['street'] : '') ?></span>
                </p>    

                <p>
                    <label for="city" 
                    class=<?=(in_array('city', $required) ? 'required' : '') ?>>City</label> <br/>
                    <input type="text" 
                           name="city" 
                           id="city"
                           class="small_field"
                           placeholder="Enter your city"
                           value="<?=clean('city')?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['city']) ? $errors['city'] : '') ?></span>
                </p>    

                <p>
                    <label for="province" 
                           class=<?=(in_array('city', $required) ? 'required' : '') ?>
                           >Province</label> <br/>
                    <select name="province" id="province" class="small_field">
                        <option value="">Select a province</option>
                        <option value="AB" 
                            <?=(clean('province')=='AB') ? 'selected' : '' ?>>Alberta</option>
                        <option value="BC" 
                            <?=(clean('province')=='BC') ? 'selected' : '' ?>>British Columbia</option>
                        <option value="MB" 
                            <?=(clean('province')=='MB') ? 'selected' : '' ?>>Manitoba</option>
                        <option value="NB" 
                            <?=(clean('province')=='NB') ? 'selected' : '' ?>>New Brunswick</option>
                        <option value="NL" 
                            <?=(clean('province')=='NL') ? 'selected' : '' ?>
                            >Newfoundland and Labrador</option>
                        <option value="NS" 
                            <?=(clean('province')=='NS') ? 'selected' : '' ?>>Nova Scotia</option>
                        <option value="ON" 
                            <?=(clean('province')=='ON') ? 'selected' : '' ?>>Ontario</option>
                        <option value="PE" 
                            <?=(clean('province')=='PE') ? 'selected' : '' ?>>Prince Edward Island</option>
                        <option value="QC" 
                            <?=(clean('province')=='QC') ? 'selected' : '' ?>>Quebec</option>
                        <option value="SK" 
                            <?=(clean('province')=='SK') ? 'selected' : '' ?>>Saskatchewan</option>
                        <option value="NT" 
                            <?=(clean('province')=='NT') ? 'selected' : '' ?>>Northwest Territories</option>
                        <option value="NU" 
                            <?=(clean('province')=='NU') ? 'selected' : '' ?>>Nunavut</option>
                        <option value="YT" 
                            <?=(clean('province')=='YT') ? 'selected' : '' ?>>Yukon</option>
                        <option value="Other" 
                            <?=(clean('province')=='Other') ? 'selected' : '' ?>>Other</option>

                    </select>
                    <span class="error"><?=(!empty($errors['province']) ? $errors['province'] : '') ?></span>
                <p>

                <p>
                    <label for="country" 
                    class=<?=(in_array('country', $required) ? 'required' : '') ?>
                    >Country</label> <br/>
                    <input type="text" 
                           name="country" 
                           id="country"
                           class="small_field"
                           placeholder="Enter your country"
                           value="<?=clean('country')?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['country']) ? $errors['country'] : '') ?></span>
                </p>     

                <p>
                    <label for="postal_code" 
                    class=<?=(in_array('postal_code', $required) ? 'required' : '') ?>
                    >Postal Code</label> <br/>
                    <input type="text" 
                           name="postal_code" 
                           id="postal_code"
                           class="small_field"
                           placeholder="Enter your postal code"
                           value="<?=clean('postal_code')?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['postal_code']) ? $errors['postal_code'] : '') ?></span>
                </p>        

                <p>
                    <label for="birthday" 
                    class=<?=(in_array('birthday', $required) ? 'required' : '') ?>
                    >Birthday</label> <br/>
                    <input type="date" 
                           name="birthday" 
                           id="birthday"
                           value="<?=clean('birthday')?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['birthday']) ? $errors['birthday'] : '') ?></span>
                </p> 

                <p>
                    <label for="password" 
                    class=<?=(in_array('password', $required) ? 'required' : '') ?>
                    >Password</label> <br/>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="small_field"
                           placeholder="Enter your password"
                           value="<?=clean('password')?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['password']) ? $errors['password'] : '') ?></span>
                </p>     

                <p>
                    <label for="confirm_password" 
                    class=<?=(in_array('confirm_password', $required) ? 'required' : '') ?>
                    >Confirm Password</label> <br/>
                    <input type="password" 
                           name="confirm_password" 
                           id="confirm_password"
                           class="small_field"
                           placeholder="Confirm your password"
                           value="<?=clean('confirm_password')?>"
                    />
                    <span class="error">
                        <?=(!empty($errors['confirm_password']) ? $errors['confirm_password'] : '') ?>
                    </span>
                </p>   
                <fieldset style="width: 350px; display: block;">
                    <legend class="required">Are you?</legend>
                        
                        <label>
                            <input type="checkbox"
                                   id="areyou_student"
                                   name="areyou[]"
                                   value="student" 
                                   onclick="user_type()"
                                    <?php
                                    if (!empty($_POST['areyou']) &&
                                         in_array('student', $_POST['areyou'])) {
                                        echo 'checked';
                                    } ?>/>
                            Student
                        </label> &nbsp;

                        <label>
                            <input type="checkbox"
                                   id="areyou_instructor"
                                   name="areyou[]"
                                   value="instructor" 
                                   onclick="user_type()"
                                    <?php
                                    if (!empty($_POST['areyou']) &&
                                        in_array('instructor', $_POST['areyou'])) {
                                        echo 'checked';
                                    } ?>/>
                            Instructor
                        </label> &nbsp;

                        <label>
                            <input type="checkbox"
                                   id="areyou_other"
                                   name="areyou[]"
                                   value="other" 
                                   onclick="user_type()"
                                    <?php
                                    if (!empty($_POST['areyou']) && in_array('other', $_POST['areyou'])) {
                                        echo 'checked';
                                    } ?>/>
                            Other
                        </label> <br/>
                        <span class="error" 
                        style="display:inline-block;font-weight:normal;">
                        <?=(!empty($errors['areyou']) ? $errors['areyou'] : '') ?></span>

                </fieldset>

                <fieldset id="fs_student" style="width: 98%;">
                    <legend>Student</legend>
                    <p>
                        <label for="parent_guardian" 
                        class=<?=(in_array('parent_guardian', $required) ? 'required' : 'norequired') ?>
                        >Parent/Guardian Name</label> <br/>
                        <input type="text" 
                               name="parent_guardian" 
                               id="parent_guardian" 
                               class="small_field"
                               placeholder="Enter parent/guardian name"
                               value="<?=clean('parent_guardian')?>"
                        />
                        <span class="error">
                            <?=(!empty($errors['parent_guardian']) ? $errors['parent_guardian'] : '') ?>
                        </span>
                    </p>

                    <p>
                        <label for="parent_guardian_phone" 
                        class=<?=(in_array('parent_guardian_phone', $required) ?
                        'required' : 'norequired') ?>>Parent/Guardian Phone</label> <br/>
                        <input type="text" 
                               name="parent_guardian_phone" 
                               id="parent_guardian_phone" 
                               class="small_field"
                               placeholder="Enter parent/guardian phone"
                               value="<?=clean('parent_guardian_phone')?>"
                        />
                        <span class="error">
                            <?=(!empty($errors['parent_guardian_phone']) ?
                            $errors['parent_guardian_phone'] : '') ?>
                          </span>
                    </p>
                </fieldset>

                <fieldset id="fs_instructor" style="width: 98%; ">
                    <legend>Instructor</legend>

                    <p>
                        <label for="resume" 
                        class=<?=(in_array('resume', $required) ? 'required' : 'norequired') ?>>Resume</label> <br/>
                        <textarea rows="5" 
                                  id="resume" 
                                  class="large_field"
                                  placeholder="Enter your resume"
                                  name="resume"><?=(clean('resume'))?></textarea> 
                        <br/>
                        <span class="error">
                                <?=(!empty($errors['resume']) ? $errors['resume'] : '') ?></span>
                    </p>

                </fieldset>

                 <p>
                    <input value="Send Form" type="submit" />
                    <input value="Clear Form" type="reset" />
                </p>
                
            </form>

        </main>
    </div>

    <script>
        /*
         * Function: user_type()
         * Shows/Hide additional fields, according by user type choosen
         */
        function user_type(){

            id_fs_stu = document.getElementById("fs_student");
            id_fs_ins = document.getElementById("fs_instructor");

            
            id_fs_stu.style.display = 'none';
            id_fs_ins.style.display = 'none';    
            
            // If "Student" option is clicked, shows fieldset for student
            if(document.getElementById("areyou_student").checked){
                id_fs_stu.style.display = 'block';
            } else {
                // If "Student" option is not clicked, clean additional fields for students 
                document.getElementById("parent_guardian").value = "" ;
                document.getElementById("parent_guardian_phone").value = "" ;
            }

            // If "Instructor" option is clicked, shows fieldset for instructor
            if(document.getElementById("areyou_instructor").checked){
                id_fs_ins.style.display = 'block';
            } else {
                // If "Instructor" option is not clicked, clean additional field for instructor
                document.getElementById("resume").value = "" ;
            }

        }

    </script>

    <!-- Calls the function user_type() to show/hide student/instructors fields, when the page is loaded -->
    <?php echo "<script> user_type(); </script>"; ?>
    
    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
