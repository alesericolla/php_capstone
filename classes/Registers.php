<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: Registers
 * Student: Alessandra Diniz
 * Date: May/13/2019
 */
namespace classes;

use classes\Database;

class Registers extends Database
{
    /**
     * The name of the table
     * @var string
     */
    protected $table = 'registers';

    /**
     * The name of the view that joins foreign key tables
     * @var string
     */
    protected $view = 'registers_vw';

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
    public function findStudent($search = null)
    {
        $query = "SELECT * FROM {$this->view} ";
        if (!is_null($search)) {
            $query = $query .
                     " WHERE id_student = :key ";
            $params = array(':key' =>  $search);
        } else {
            $params = [];
        }

        $stmt = static::$dbh->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

// end class
}
