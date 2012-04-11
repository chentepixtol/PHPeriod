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
    public function main(){
        $period = new Period();
        $this->assertTrue($period instanceof Period);
    }
}

