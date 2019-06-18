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

class Fees extends Database
{
    /**
     * The name of the table
     * @var string
     */
    protected $table = 'fees';

    /**
     * The name of the view that joins foreign key tables
     * @var string
     */
    protected $view = 'fees_vw';

    /**
     * Field to use when finding one record by id
     * @var string
     */
    protected $key = 'id_fee';

    /**
     * Field to use when finding one record by name
     * @var string
     */
    protected $key_name = 'fee_name';


// end class
}
