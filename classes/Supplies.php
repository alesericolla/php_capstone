<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: Supplies
 * Student: Alessandra Diniz
 * Date: May/16/2019
 */
namespace classes;

use classes\Database;

class Supplies extends Database
{
    /**
     * The name of the table
     * @var string
     */
    protected $table = 'supplies';

    /**
     * The name of the view that joins foreign key tables
     * @var string
     */
    protected $view = 'supplies_vw';

    /**
     * Field to use when finding one record by id
     * @var string
     */
    protected $key = 'id_supplies';

    /**
     * Field to use when finding one record by name
     * @var string
     */
    protected $key_name = 'required_supplies';

// end class
}
