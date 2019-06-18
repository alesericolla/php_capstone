<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: Classes
 * Student: Alessandra Diniz
 * Date: May/13/2019
 */
namespace classes;

use classes\Database;

class Classes extends Database
{
    /**
     * The name of the table
     * @var string
     */
    protected $table = 'classes';

    /**
     * The name of the view that joins foreign key tables
     * @var string
     */
    protected $view = 'classes_vw';

    /**
     * Field to use when finding one record by id
     * @var string
     */
    protected $key = 'id_class';

    /**
     * Field to use when finding one record by name
     * @var string
     */
    protected $key_name = 'class_name';


// end class
}
