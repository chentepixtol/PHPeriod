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
        $startDate = $day.' 00:00:00';
        $endDate = $day.' 23:59:59';
        parent::__construct($startDate, $endDate, $format);
    }

}