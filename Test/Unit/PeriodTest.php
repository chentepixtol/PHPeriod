<?php

namespace Test\Unit;

use PHPeriod\Day;

use PHPeriod\PeriodCollection;
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
        $period = $this->get2012Period();
        $this->assertTrue($period instanceof Period);
        $this->assertEquals('2012-01-01 00:00:00 to 2012-12-23 23:59:59', $period->getIndex());
    }

    /**
     * @test
     */
    public function adjust(){
        $period = $this->get2012Period();

        $period->adjustStartDate('2012-01-10 09:15:00');
        $this->assertEquals('2012-01-10 09:15:00 to 2012-12-23 23:59:59', $period->getIndex());

        $period->adjustEndDate('2012-10-19 21:20:00');
        $this->assertEquals('2012-01-10 09:15:00 to 2012-10-19 21:20:00', $period->getIndex());

        $period = $this->get2012Period();
        $period->adjust('2012-01-10 09:15:00', '2012-10-19 21:20:00');
        $this->assertEquals('2012-01-10 09:15:00 to 2012-10-19 21:20:00', $period->getIndex());
    }

    /**
     * @test
     */
    public function getElapsedSeconds(){
        $day = new Day('2012-01-01');
        $this->assertEquals(86399, $day->getElapsedSeconds());
    }

    /**
     * @test
     */
    public function getDuration(){
        $day = new Day('2012-01-01');
        $this->assertEquals("23 hours 59 minutes 59 seconds", $day->getDuration()->toHuman());
    }

    /**
     * @test
     */
    public function toCollection(){
        $period = $this->get2012Period();
        $collection = $period->toCollection();
        $this->assertTrue($collection instanceof PeriodCollection);
        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     * @expectedException \PHPeriod\Exception
     */
    public function invalidAdjust(){
        $period = $this->get2012Period();
        $period->adjustStartDate('2015-01-10 09:15:00');
    }

    /**
     * @test
     */
    public function comparisions(){
        $period = $this->get2012February();

        $this->assertTrue($period->isLowerOrEqual($period->getEndDate(), $period->getStartDate()));
        $this->assertFalse($period->isLowerOrEqual($period->getStartDate(), $period->getEndDate()));

        $this->assertFalse($period->isGreaterOrEqual($period->getEndDate(), $period->getStartDate()));
        $this->assertTrue($period->isGreaterOrEqual($period->getStartDate(), $period->getEndDate()));
    }

    /**
     * @test
     */
    public function isInside(){
        $year2012 = $this->get2012Period();
        $february = $this->get2012February();
        $this->assertTrue($february->isInside($year2012));
        $this->assertFalse($year2012->isInside($february));
    }

    /**
     * @test
     */
    public function isCoveringTo(){
        $year2012 = $this->get2012Period();
        $february = $this->get2012February();

        $this->assertTrue($year2012->isCoveringTo($february));
        $this->assertFalse($february->isCoveringTo($year2012));
    }


    /**
     * @test
     */
    public function isLeftSide(){
        $february = $this->get2012February();
        $december = $this->get2012December();

        $this->assertTrue($february->isLeftSideFrom($december));
        $this->assertFalse($december->isLeftSideFrom($february));
    }

    /**
     * @test
     */
    public function isRightSide(){
        $february = $this->get2012February();
        $january = $this->get2012January();

        $this->assertTrue($february->isRightSideFrom($january));
        $this->assertFalse($january->isRightSideFrom($february));
    }

    /**
     * @test
     */
    public function isPartiallyLeftSide(){
        $periodLeft = new Period("2012-01-01 00:00:00", "2012-01-15 23:59:59");
        $periodRigth = new Period("2012-01-11 00:00:00", "2012-01-20 23:59:59");
        $this->assertTrue($periodLeft->isPartiallyLeftSide($periodRigth));
        $this->assertFalse($periodRigth->isPartiallyLeftSide($periodLeft));
        $this->assertFalse($this->get2012January()->isPartiallyLeftSide($this->get2012February()));
    }

    /**
     * @test
     */
    public function isPartiallyRigthSide(){
        $periodLeft = new Period("2012-01-01 00:00:00", "2012-01-15 23:59:59");
        $periodRigth = new Period("2012-01-11 00:00:00", "2012-01-20 23:59:59");

        $this->assertTrue($periodRigth->isPartiallyRigthSide($periodLeft));
        $this->assertFalse($periodLeft->isPartiallyRigthSide($periodRigth));
        $this->assertFalse($this->get2012February()->isPartiallyRigthSide($this->get2012January()));
    }

    /**
     * @test
     * @expectedException \PHPeriod\Exception
     */
    public function invalidPeriod(){
        $period = new Period("2013-04-11 09:15:00", "2012-12-23 23:59:59");
    }

    /**
     * @test
     */
    public function day(){
        $day = new Day('2012-04-01');
        $this->assertEquals('2012-04-01 00:00:00 to 2012-04-01 23:59:59', $day->toString());
    }

    /**
     * @test
     * @dataProvider getSubtractPeriods
     */
    public function subtract($periodStart, $periodEnd, $expected){
        $sub = new Period($periodStart, $periodEnd);
        $period = new Period("2012-04-11 00:00:00", "2012-04-20 23:59:59");
        $this->assertEquals($expected, $period->subtract($sub)->toString());
    }

    /**
     * @test
     * @dataProvider getInterceptPeriods
     */
    public function intersect($periodStart, $periodEnd, $expected){
        $sub = new Period($periodStart, $periodEnd);
        $period = new Period("2012-04-11 00:00:00", "2012-04-20 23:59:59");
        $this->assertEquals($expected, $period->intersect($sub)->toString());
    }

    /**
     * @return array
     */
    public function getInterceptPeriods(){
        return array(
            array('2012-04-01 00:00:00', '2012-04-25 23:59:59', '2012-04-11 00:00:00 to 2012-04-20 23:59:59'),
            array('2012-04-08 00:00:00', '2012-04-09 23:59:59', ''),
            array('2012-04-08 00:00:00', '2012-04-12 23:59:59', '2012-04-11 00:00:00 to 2012-04-12 23:59:59'),
            array('2012-04-12 00:00:00', '2012-04-18 23:59:59', '2012-04-12 00:00:00 to 2012-04-18 23:59:59'),
            array('2012-04-18 00:00:00', '2012-04-23 23:59:59', '2012-04-18 00:00:00 to 2012-04-20 23:59:59'),
            array('2012-04-23 00:00:00', '2012-04-25 23:59:59', ''),
    );
    }

    /**
     *
     */
    public function getSubtractPeriods(){
        return array(
            array('2012-04-01 00:00:00', '2012-04-25 23:59:59', ''),
            array('2012-04-08 00:00:00', '2012-04-09 23:59:59', '2012-04-11 00:00:00 to 2012-04-20 23:59:59'),
            array('2012-04-08 00:00:00', '2012-04-12 23:59:59', '2012-04-13 00:00:00 to 2012-04-20 23:59:59'),
            array('2012-04-12 00:00:00', '2012-04-18 23:59:59', '2012-04-11 00:00:00 to 2012-04-11 23:59:59,2012-04-19 00:00:00 to 2012-04-20 23:59:59'),
            array('2012-04-18 00:00:00', '2012-04-23 23:59:59', '2012-04-11 00:00:00 to 2012-04-17 23:59:59'),
            array('2012-04-23 00:00:00', '2012-04-25 23:59:59', '2012-04-11 00:00:00 to 2012-04-20 23:59:59'),
        );
    }

}


