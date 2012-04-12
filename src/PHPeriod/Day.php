<?php

namespace PHPeriod;

/**
 *
 * @author chente
 *
 */
class Day extends Period
{

    /**
     *
     * @param string $day
     */
    public function __construct($day){
        if( !\Zend_Date::isDate($day, 'yyyy-MM-dd') ){
            throw new Exception("The day is invalid ".$day);
        }
        $startDate = new \Zend_Date($day.' 00:00:00', 'yyyy-MM-dd HH:mm:ss');
        $endDate = new \Zend_Date($day.' 23:59:59', 'yyyy-MM-dd HH:mm:ss');
        parent::__construct($startDate, $endDate);
    }

}