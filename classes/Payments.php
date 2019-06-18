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

class Payments extends Database
{
    /**
     * The name of the table
     * @var string
     */
    protected $table = 'payments';

    /**
     * The name of the view that joins foreign key tables
     * @var string
     */
    protected $view = 'payments_vw';

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
    public function findStudent($search = null, $search_id_payment = null)
    {
        $params = [];
        $query = "SELECT * FROM {$this->view} ";
        if (!is_null($search)) {
            $query = $query .
                     " WHERE id_student = :key ";
            $params[':key'] = $search;
        } else {
        }

        if (!is_null($search_id_payment)) {
            $query = $query .
                     " AND id_payment = :keypayment ";
            $params[':keypayment'] = $search_id_payment;
        }

        $query = $query .
         " ORDER BY payment_month DESC";

        var_dump($query);
        $stmt = static::$dbh->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Returns detail information about a payment
     * @return Array
     */
    public function findPaymentDetail($search = null)
    {
        $params = [];

        $query = "SELECT * FROM payments_detail ";
        if (!is_null($search)) {
            $query = $query .
                     " WHERE id_payment = :key ";
            $params[':key'] = $search;
        }

        $stmt = static::$dbh->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

// end class
}
