<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Page: admin_sched.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

//Variables declaration
$page_title = 'Schedules - Admin';
$class_admin = 'admin';

include __DIR__ . '/../conf/config.php';
include __DIR__ . '/../lib/functions.php';

use classes\Schedules;

//Verify if the user is logged as an Admin user
if (empty($_SESSION['admin']) or $_SESSION['admin'] == 0) {
    $_SESSION['flash_msg'] = array(
    'type' => 'error',
    'message' => "You need to be logged in as an Admin user to access this page!");
    header('Location: login.php');
    die();
}

$num_column = 0;
$link = basename($_SERVER['PHP_SELF']) . '?';


$detail_page = str_replace(".php", "", basename($_SERVER['PHP_SELF'])) . "_dtl.php";

$num_rec = 20;
$search = '';
$actual_page = 1;

if (!empty(cleang('num_rec'))) {
    $num_rec = cleang('num_rec');
    $link = $link . "&num_rec=" . $num_rec;
}

if (!empty(cleang('search'))) {
    $search = cleang('search');
    $link = $link . "&search=" . $search;
}
if (!empty(cleang('p'))) {
    $actual_page = cleang('p');
}
//}

$tbschedules = new Schedules($dbh);

//Get total number of records
$schedules_list_complete = $tbschedules->allView($search);
$tot_rec = count($schedules_list_complete);
if ($tot_rec>$num_rec) {
    $num_pages = intval($tot_rec/$num_rec) + (($tot_rec%$num_rec==0)?0:1);
} else {
    $num_pages = 1;
}

//Get list according the page number
$schedules_list = $tbschedules->allView($search, $num_rec, $actual_page);
$table_fields = ['class_name', 'week_days', 'begin_time', 'end_time', 'age_range', 'room_name'];

$previous_page = ($actual_page==1) ? 1 : $actual_page - 1;
$next_page = ($actual_page==$num_pages) ? $num_pages : $actual_page + 1;

?><!doctype html>

<!-- Head -->
<?php include __DIR__ . '/../inc/head_inc.php';?> 

<!-- Admin Navigation --> 
<?php include __DIR__ . '/../inc/admin_inc.php';?> 
    
    <div id="wrapper">  
        <!-- Main -->
        <main>
            <h1><?=$page_title?></h1>

            <table class="no_border">
                <tr>
                    <td>
                        <form action=<?=$detail_page?>
                              method="post">
                            <input type="submit" class="size_var"
                                   name="action"
                                   value="Add New"/>  
                            <input type="hidden" 
                                   name="id" 
                                   value="new"/>
                        </form>
                    </td>

                    <form action="<?=basename($_SERVER['PHP_SELF'])?>" 
                          method="get"
                          autocomplete="on"
                          name="reload_form"
                          novalidate>

                        <td class="align_center">
                            <p>
                                <label for="num_rec">Records per page</label>&nbsp;
                                <select name="num_rec" id="num_rec" class="small_field" onchange="update_num_rec()">
                                    <option value="5" 
                                        <?=($num_rec=='5') ? 'selected' : '' ?>>5</option>
                                    <option value="10" 
                                        <?=($num_rec=='10') ? 'selected' : '' ?>>10</option>
                                    <option value="20" 
                                        <?=($num_rec=='20') ? 'selected' : '' ?>>20</option>  
                                    <option value="50"
                                        <?=($num_rec=='50') ? 'selected' : '' ?>>50</option>
                                </select>                  
                                
                            </p>
                        </td>
                        <td class="align_right">
                            <input type="text" 
                                   name="search" 
                                   id="search"
                                   class="medium_field"
                                   value="<?=cleang('search')?>"
                            /> 
                            <button>Search</button><br/>                                                
                        </td>
                    </form>
                </tr>
            </table>                                                

            <table>
                <tr>
                    <?php foreach ($table_fields as $key => $value) : ?>
                        <th><?=format_label($value)?></th>
                    <?php endforeach ?>
                    <th>Actions</th>

                </tr>

                <?php foreach ($schedules_list as $key => $row) : ?>
                    <tr>
                        <?php
                        foreach ($table_fields as $keylist => $value) {
                            echo '<td>'. rtrim($row[$value], ';') . '</td>';
                        }
                        ?>

                        <td>
                            <form action=<?=$detail_page?>
                                  method="post">
                                    <input type="submit" class="size_var"
                                           name="action"
                                           value="Edit"
                                    />
                                    <input type="submit" class="size_var"
                                           name="action"
                                           value="Delete"
                                    />                                          
                                    <input type="hidden" 
                                           name="id_schedule" 
                                           value=<?=reset($row)?>
                                    />
                            </form>
                        </td>
                    </tr>
                    
                <?php endforeach ?>

            </table>

            <table class="no_border">
                <tr>
                    <td align="right">
                        <?php
                            $pages = "<a href='" .
                                     $link ."&p=1'> " .
                                     "<button>&lt;&lt;</button></a> &nbsp; ";

                            $pages = $pages. "<a href='" .
                                     $link ."&p=" . $previous_page . "'> " .
                                     "<button>&lt;</button></a> &nbsp; ";

                        for ($i=1; $i<=$num_pages; $i++) {
                            $pages = $pages. "<a href='" .
                                 $link . "&p=" . $i . "'> " .
                                 "<button" .
                                 (($i==$actual_page)? " class='actual_page'" : "") .
                                 ">$i</button></a> &nbsp; ";
                        }
                            $pages = $pages. "<a href='" .
                                     $link . "&p=" . $next_page . "'> " .
                                     "<button>&gt;</button></a> &nbsp; ";

                            $pages = $pages. "<a href='" .
                                     $link ."&p=" . $num_pages . "'> " .
                                     "<button>&gt;&gt;</button></a> &nbsp; ";
                            echo $pages;

                        ?>
                    </td>
                </tr>
            </table>
                             
        </main>
    </div>

    <script>
        /**
         * Reload the page according of updated number of records 
         * @return Void
         */
        function update_num_rec(){
            var num_rec = document.getElementById('num_rec').value;
            var link =  <?=("'" . basename($_SERVER['PHP_SELF']) . "'") ?> + '?&num_rec=' + num_rec;
            var myWindow = window.open(link, "_self");
        }
    </script>
 
    <!-- Footer -->
    <?php include __DIR__ . '/../inc/footer_inc.php';?> 