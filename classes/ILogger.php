<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Class: ILogger
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */
namespace classes;

interface ILogger
{

    public function write($event);

    public function read();
}
