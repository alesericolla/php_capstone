<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: Rooms
 * Student: Alessandra Diniz
 * Date: May/16/2019
 */
namespace classes;

use classes\Database;

class Instructors extends Database
{
    /**
     * The name of the table
     * @var string
     */
    protected $table = 'instructors';

    /**
     * Field to use when finding one record by id
     * @var string
     */
    protected $key = 'id_instructor';

    /**
     * The name of the view that joins foreign key tables
     * @var string
     */
    protected $view = 'instructors_vw';

// end class
}
