<?php

namespace Test\Unit;

use PHPeriod\Period;

/**
 *
 * @author chente
 *
 */
class PeriodTest extends BaseTest
{

    /**
     * @test
     */
    public function construction(){
        $period = $this->getAll2012Period();
        $this->assertTrue($period instanceof Period);
        $this->assertEquals('2012-01-01 00:00:00 to 2012-12-23 23:59:59', $period->getIndex());
    }

    /**
     * @test
     */
    public function adjust(){
        $period = $this->getAll2012Period();

        $period->adjustStartDate(new \Zend_Date('2012-01-10 09:15:00'));
        $this->assertEquals('2012-01-10 09:15:00 to 2012-12-23 23:59:59', $period->getIndex());

        $period->adjustEndDate(new \Zend_Date('2012-10-19 21:20:00'));
        $this->assertEquals('2012-01-10 09:15:00 to 2012-10-19 21:20:00', $period->getIndex());

        $period = $this->getAll2012Period();
        $period->adjust(new \Zend_Date('2012-01-10 09:15:00'), new \Zend_Date('2012-10-19 21:20:00'));
        $this->assertEquals('2012-01-10 09:15:00 to 2012-10-19 21:20:00', $period->getIndex());
    }

    /**
     * @test
     * @expectedException \PHPeriod\Exception
     */
    public function invalidAdjust(){
        $period = $this->getAll2012Period();
        $period->adjustStartDate(new \Zend_Date('2015-01-10 09:15:00'));
    }

    /**
     * @test
     * @expectedException \PHPeriod\Exception
     */
    public function invalidPeriod(){
        $period = new Period(new \Zend_Date("2013-04-11 09:15:00"), new \Zend_Date("2012-12-23 23:59:59"));
    }


}


