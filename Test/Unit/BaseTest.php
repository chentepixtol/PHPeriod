<?php

namespace Test\Unit;

use PHPeriod\Period;

/**
 *
 * @author chente
 *
 */
abstract class BaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return Period
     */
    protected function getAll2012Period(){
        return new Period(new \Zend_Date("2012-01-01 00:00:00"), new \Zend_Date("2012-12-23 23:59:59"));
    }

}

