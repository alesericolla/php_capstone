<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Log.php
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */
namespace classes;

use classes\ILogger;
use classes\DatabaseLogger;
use classes\FileLogger;

$logfile = __DIR__ . '/../storage/log.txt';
$logsqlite = __DIR__ . '/../storage/log.sqlite';

// To alter the type of log file it's necessary to comment the following lines:
//$logger = new FileLogger($logfile); //log.txt
//$logger = new DatabaseLogger(new \PDO('sqlite:'. $logsqlite)); //sqlite
$logger = new DatabaseLogger($dbh); //mysql

/**
 * Creates the event string to be storage in log file/database
 * @param  ILogger $logger Connection to store log information (text file, mysql or sqlite table)
 * @return Void
 */
function logEvent(ILogger $logger)
{
    
    $dttime         = $_SERVER['REQUEST_TIME'];
    $format_dttime = date('Y-m-d H:i:s', $dttime);

    $event = "[{$format_dttime}]";

    $event = $event . " - [REMOTE_ADDR: {$_SERVER["REMOTE_ADDR"]}]";

    $event = $event . " - [REQUEST_URI: {$_SERVER["REQUEST_URI"]}]";

    $event = $event . " - [HTTP_USER_AGENT: {$_SERVER["HTTP_USER_AGENT"]}]";

    $resp_code = http_response_code();
    $event = $event . " - [HTTP RESPONSE CODE: {$resp_code}]";


    if (!strpos($event, 'favicon')) {
        $logger->write($event);
    }
}

logEvent($logger);
