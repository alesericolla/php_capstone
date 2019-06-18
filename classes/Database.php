<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: Database
 * Student: Alessandra Diniz
 * Date: May/13/2019
 */
namespace classes;

class Database
{

    protected static $dbh;

    /**
     * Initialize model by storing database connection
     * @param  \PDO   $dbh
     * @return Void
     */
    public function __construct(\PDO $dbh)
    {
        static::$dbh = $dbh;
    }

    /**
     * Returns all records from a table
     * @return Array
     */
    public function all()
    {
        $query = 'SELECT * FROM ' . $this->table;
        //temporaly it is asking about deleted field only for classes table
        //when the field deleted is inserted in every table, this line must be removed
        if ($this->table=='classes') {
            $query = $query . ' WHERE deleted = 0 ';
        }
        $stmt = static::$dbh->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Returns all records from the view that joins the main table and its foreign key tables
     * @return Array
     */
    public function allView($search = null, $num_rec = null, $num_page = 1)
    {
        $query = "SELECT * FROM {$this->view} ";
        if (!is_null($search)) {
            $query = $query .
                     " WHERE {$this->key_name} LIKE :key_name ";
            $params = array(':key_name' =>  "%{$search}%");
        } else {
            $params = [];
        }
        if (!is_null($num_rec)) {
            $first_record = (($num_page - 1) * $num_rec);
            $query = $query . ' LIMIT ' . $first_record . ', ' . $num_rec;
        }

        $stmt = static::$dbh->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Returns a single record based on the designated key,
     * from the view that joins the main table and its foreign key tables
     * @param  String $key
     * @return Array
     */
    public function findView($key)
    {
        $query = "SELECT * FROM {$this->view} WHERE {$this->key} = :key";
        $stmt = static::$dbh->prepare($query);
        $params = array(':key' => $key);
        $stmt->execute($params);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Returns a single record based on the designated key
     * @param  String $key
     * @return Array
     */
    public function find($key)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$this->key} = :key";
        $stmt = static::$dbh->prepare($query);
        $params = array(':key' => $key);
        $stmt->execute($params);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
