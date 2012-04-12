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
    protected function get2012Period(){
        return new Period(new \Zend_Date("2012-01-01 00:00:00", 'yyyy-MM-dd HH:mm:ss'), new \Zend_Date("2012-12-23 23:59:59", 'yyyy-MM-dd HH:mm:ss'));
    }

    /**
     * @return Period
     */
    protected function get2012February(){
        return new Period(new \Zend_Date("2012-02-01 00:00:00", 'yyyy-MM-dd HH:mm:ss'), new \Zend_Date("2012-02-29 23:59:59", 'yyyy-MM-dd HH:mm:ss'));
    }

    /**
     * @return Period
     */
    protected function get2012January(){
        return new Period(new \Zend_Date("2012-01-01 00:00:00", 'yyyy-MM-dd HH:mm:ss'), new \Zend_Date("2012-01-31 23:59:59", 'yyyy-MM-dd HH:mm:ss'));
    }

    /**
     * @return Period
     */
    protected function get2012December(){
        return new Period(new \Zend_Date("2012-12-01 00:00:00", 'yyyy-MM-dd HH:mm:ss'), new \Zend_Date("2012-12-31 23:59:59", 'yyyy-MM-dd HH:mm:ss'));
    }

}

