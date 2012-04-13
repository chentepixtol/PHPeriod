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
    public function __construct($day, $format = Period::MYSQL_FORMAT){
        if( !\Zend_Date::isDate($day, 'yyyy-MM-dd') ){
            throw new Exception("The day is invalid ".$day);
        }
        $startDate = $day.' 00:00:00';
        $endDate = $day.' 23:59:59';
        parent::__construct($startDate, $endDate, $format);
    }

}