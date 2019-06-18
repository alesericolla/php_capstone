<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Functions.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */

/**
 *  Escape string
 * @param  String    $string       - String to be escaped
 * @return String                  - String escaped
 */
function esc($string)
{
    return htmlentities($string, null, 'UTF-8');
}

/**
 *  Escape attribute string
 * @param  String    $string       - String for attribute to be escaped
 * @return String                  - String escaped
 */
function esc_attr($string)
{
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

/**
 *  Clean string from $_POST variable
 * @param  String    $field          - Field form that will be clean
 * @return String                    - String cleaned of special characters
 */
function clean($field)
{
    if (filter_input(INPUT_POST, $field)) {
        return htmlentities(filter_input(INPUT_POST, $field), ENT_QUOTES, "UTF-8");
    } else {
        return '';
    }
}

/**
 *  Clean string from $_GET variable
 * @param  String    $field         - Field form that will be clean
 * @return String                    - String cleaned of special characters
 */
function cleang($field)
{
    if (filter_input(INPUT_GET, $field)) {
        return htmlentities(filter_input(INPUT_GET, $field), ENT_QUOTES, "UTF-8");
    } else {
        return '';
    }
}

/**
 *  Clean string
 * @param  String    $string       - String to be clean
 * @return String                  - String cleaned of special characters
 */
function format_label($key)
{
    return ucwords(str_replace('_', ' ', $key));
}

/**
 * Function: execute_query
 * Executes a query in database
 * @param string    $query        - Query to be executed
 * @param array     $params_list  - List of query parameters
 * @param int       $new_id       - It is an optional parameter,
 *        if it is sended, it will be filled with the new id inserted
 * @return boolean
 */
function execute_query($dbh, $query, $params_list, &$new_id = null, &$fetch = null)
{

    try {
        // prepare query
        $stmt = $dbh->prepare($query);

        // execute query
        $stmt->execute($params_list);

        // if new_id is not null, return last inserted ID
        if (isset($new_id)) {
             $new_id = $dbh->lastInsertId();
        }
    } catch (Exception $e) {
        if ($dbh->inTransaction()) {
            $dbh->rollBack();
        }
        die($e->getMessage());
    }

    return true;
}

/**
 * Get Token Value
 * @return String                    - Token value
 */
function getToken()
{
    if (!empty($_SESSION['csrf_token'])) {
        return htmlentities($_SESSION['csrf_token'], ENT_QUOTES, "UTF-8");
    } else {
        return null;
    }
}

/**
 *  Encrypt Password
 * @param  String    $field        - Password form filed to be encrypted
 * @return String                    - Password encrypted
 */
function encryptPass($field)
{
    return password_hash(filter_input(INPUT_POST, $field), PASSWORD_DEFAULT);
}


/**
 * Fill the field with the previous value input available in $_POST variable
 * or in case of EDIT or DELETE, for the first time, fill with table values
 * @param  [type] $field [description]
 * @return Previous value of the field
 */
function previous_value($field, $table_values)
{

    if (empty(clean($field))) {
        if (!empty($table_values[$field])) {
            return esc($table_values[$field]);
        } else {
            return '';
        }
    } else {
        return clean($field);
    }
}

/**
 * Fill the field that expects an array with the previous value input available in $_POST variable
 * or in case of EDIT or DELETE, for the first time, fill with table values
 * @param  [type] $field [description]
 * @return Previous value of the field
 */
function previous_value_array($field, $table_values)
{

    $field_esc = filter_input(INPUT_POST, $field, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    if (empty($field_esc)) {
        if (!empty($table_values[$field])) {
            return $table_values[$field];
        } else {
            return [];
        }
    } else {
        return $field_esc;
    }
}
