<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: Dashboard
 * Student: Alessandra Diniz
 * Date: May/16/2019
 */
namespace classes;

use classes\Database;

class Dashboard extends Database
{

    /**
     * Returns all records from a dashboard view
     * @return Array
     */
    public function allDashboard($table)
    {
        $query = 'SELECT * FROM ' . $table;
        $stmt = static::$dbh->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
