<?php

namespace Test\Unit;

use PHPeriod\Duration;

use PHPeriod\PeriodCollection;
use PHPeriod\Period;

/**
 *
 * @author chente
 *
 */
class DurationTest extends BaseTest
{
    const DAYS = 2;
    const HOURS = 12;
    const MINUTES = 27;
    const SECONDS = 16;

    /**
     * @test
     */
    public function main(){
        $duration = new Duration($this->getSeconds());
        $this->assertEquals(self::DAYS, $duration->getDaysPart());
        $this->assertEquals(self::HOURS, $duration->getHoursPart());
        $this->assertEquals(self::MINUTES, $duration->getMinutesPart());
        $this->assertEquals(self::SECONDS, $duration->getSecondsPart());
        $this->assertEquals("2 days 12 hours 27 minutes 16 seconds", $duration->toHuman());
    }

    /**
     *
     */
    private function getSeconds(){
        $seconds = self::SECONDS;
        $seconds += ( self::MINUTES * 60);
        $seconds += ( self::HOURS * 3600 );
        $seconds += ( self::DAYS * 24 * 3600 );

        return $seconds;
    }

}


