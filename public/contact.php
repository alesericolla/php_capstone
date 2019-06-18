<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: contact.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Contact';

//Include Config File
include __DIR__ . '/../conf/config.php';

?><!doctype html>
 
<!-- Head -->
<?php include __DIR__ . '/../inc/head_inc.php';?> 
            
    <div id="wrapper">    
        
        <!-- Main Page -->
        <main>
            <form method="post"
                action="http://www.scott-media.com/test/form_display.php"
                autocomplete="on">
            
                <h1 class="form_title"><?=$page_title?> Form</h1>

                <p>
                    <label for="name" class="required">Name</label> <br/>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           placeholder="Enter your name"
                           required />
                </p>

                <p>
                    <label for="email" class="required">Email Address</label> <br/>
                    <input type="email" 
                           name="email" 
                           id="email"
                           placeholder="Enter your email"
                           required/>
                </p>
                <p>
                    <label for="phone">Phone Number</label> <br/>
                    <input type="tel" 
                           name="phone" 
                           id="phone" 
                           placeholder="Enter your phone number"/>
                </p>

                <p>
                    <label for="hear_about">How did you hear about us?</label> <br/>
                    <select name="hear_about" id="hear_about">
                        <option value="not_selected">Select an option</option>
                        <option value="google">Google</option>
                        <option value="friends">Friends</option>
                        <option value="online">Online Ads</option>
                        <option value="social_media">Social Media</option>
                        <option value="other">Other</option>
                    </select>
                <p>

                <p>
                    <label>Student Age</label> <br/>
                    <input list="age" name="age" />
                    <datalist id="age">
                        <option value="3-6"></option>
                        <option value="7"></option>
                        <option value="8"></option>
                        <option value="9"></option>
                        <option value="10"></option>
                        <option value="11"></option>
                        <option value="12"></option>
                        <option value="13"></option>
                        <option value="14"></option>
                        <option value="15"></option> 
                        <option value="16"></option>
                        <option value="17"></option>
                        <option value="18 and above"></option>                    
                    </datalist>
                </p>
                
                <fieldset style="width: 350px; display: inline-block;">
                    <legend>Are you interested in which program?</legend>
                    <input type="checkbox"
                           id="ballet_interest"
                           name="interest_choice1"
                           value="Ballet" />
                    <label for="ballet_interest">Ballet</label><br />

                    <input type="checkbox"
                           id="jazz_interest"
                           name="interest_choice2"
                           value="Jazz" />
                    <label for="jazz_interest">Jazz</label><br />

                    <input type="checkbox"
                           id="tap_dance_interest"
                           name="interest_choice3"
                           value="Tap Dance" />
                    <label for="tap_dance_interest">Tap Dance</label><br />

                    <input type="checkbox"
                           id="street_dance_interest"
                           name="interest_choice4"
                           value="Street Dance" />
                    <label for="street_dance_interest">Street Dance</label><br /> 
                    
                    <input type="checkbox"
                           id="salsa_interest"
                           name="interest_choice5"
                           value="Salsa" />
                    <label for="salsa_interest">Salsa</label><br /> 
                    
                    <input type="checkbox"
                           id="zumba_interest"
                           name="interest_choice6"
                           value="Zumba" />
                    <label for="zumba_interest">Zumba</label><br /> 

                    <input type="checkbox"
                           id="flamenco_interest"
                           name="interest_choice7"
                           value="Flamenco" />
                    <label for="flamenco_interest">Flamenco</label><br />

                    <input type="checkbox"
                           id="other_interest"
                           name="interest_choice8"
                           value="Other" />
                    <label for="other_interest">Other</label>
                </fieldset>

                <p>
                    <label for="message">Message</label><br />
                    <textarea rows="5" 
                              id="message" 
                              placeholder="Enter your message"
                              name="message">
                    </textarea>
                </p>

                 <p>
                    <input value="Send Form" type="submit" />
                    <input value="Clear Form" type="reset" />
                </p>   

            </form>
            
        </main>
        
    </div>
    
    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 
