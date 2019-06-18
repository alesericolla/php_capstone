<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: FileLogger
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */
namespace classes;

use classes\ILogger;

class FileLogger implements Ilogger
{

    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Write log information in text file
     * @param  [String] $event [Event string to be stored]
     * @return Void
     */
    public function write($event)
    {

        //Open the file
        $myfile = fopen($this->file, "a");

        //Write the event
        fwrite($myfile, $event . "\n");

        //Close the file
        fclose($myfile);
    }

    /**
     * Read log information events from text file
     * @return [Array] [List of the last 10 events]
     */
    public function read()
    {

        //Read the file log
        $lines = file($this->file);

        //Revert the order to get last 10 lines
        $reversed = array_reverse($lines);

        $log_display = [];

        // Loop through our array, and get first 10 lines
        foreach ($reversed as $line_num => $line) {
            array_push($log_display, htmlentities($line));
            if ($line_num==10) {
                break;
            }
        }

        return $log_display;
    }
}
