<?php

namespace Test\Unit;

use PHPeriod\PeriodCollection;
use PHPeriod\Period;

/**
 *
 * @author chente
 *
 */
class PeriodCollectionTest extends BaseTest
{

    /**
     * @test
     */
    public function toStringTest(){
        $collection = new PeriodCollection();
        $collection->append($this->get2012January());
        $collection->append($this->get2012February());
        $this->assertEquals('2012-01-01 00:00:00 to 2012-01-31 23:59:59,2012-02-01 00:00:00 to 2012-02-29 23:59:59', $collection->toString());
    }



}


