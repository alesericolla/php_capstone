<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: DatabaseLogger
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */
namespace classes;

use classes\ILogger;

class DatabaseLogger implements Ilogger
{

    protected $dbh;

    public function __construct(\PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * Write log information in the database (MySql or Sqlite)
     * @param  [String] $event [Event string to be stored]
     * @return Void
     */
    public function write($event)
    {

        try {
            // create query to insert log table
            $query = "INSERT INTO log
                      (event)
                      VALUES (:event)";

            $params = array(
              ':event' => $event
            );

            // prepare query
            $stmt = $this->dbh->prepare($query);

            // execute query
            $stmt->execute($params);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Read log information events from database (MySql or Sqlite)
     * @return [Array] [List of the last 10 events]
     */
    public function read()
    {

        try {
            // create query to select log table
            $query = "select event FROM log
                      ORDER BY id DESC 
                      LIMIT 10";

            // prepare query
            $stmt = $this->dbh->prepare($query);

            $params = [];

            // execute query
            $stmt->execute($params);

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $log_event = [];

            //Creates a new array with the events string values
            foreach ($result as $key => $row) {
                array_push($log_event, $row['event']);
            }
            return $log_event;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
