<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: Schedules
 * Student: Alessandra Diniz
 * Date: May/13/2019
 */
namespace classes;

use classes\Database;

class Schedules extends Database
{
    /**
     * The name of the table
     * @var string
     */
    protected $table = 'schedules';

    /**
     * The name of the view that joins foreign key tables
     * @var string
     */
    protected $view = 'schedules_vw';

    /**
     * Field to use when finding one record by id
     * @var string
     */
    protected $key = 'id_schedule';

    /**
     * Field to use when finding one record by name
     * @var string
     */
    protected $key_name = 'class_name';


    /**
     * Returns all records from the schedule view ordered by begin time
     * @return Array
     */
    public function allViewTime($search = null)
    {
        $query = "SELECT * FROM schedules_vw_time ";
        if (!is_null($search)) {
            $query = $query .
                     " WHERE {$this->key_name} LIKE :key_name ";
            $params = array(':key_name' =>  "%{$search}%");
        } else {
            $params = [];
        }

        $stmt = static::$dbh->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

// end class
}
