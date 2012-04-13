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
        return new Period("2012-01-01 00:00:00", "2012-12-23 23:59:59");
    }

    /**
     * @return Period
     */
    protected function get2012February(){
        return new Period("2012-02-01 00:00:00", "2012-02-29 23:59:59");
    }

    /**
     * @return Period
     */
    protected function get2012January(){
        return new Period("2012-01-01 00:00:00", "2012-01-31 23:59:59");
    }

    /**
     * @return Period
     */
    protected function get2012December(){
        return new Period("2012-12-01 00:00:00", "2012-12-31 23:59:59");
    }

}

