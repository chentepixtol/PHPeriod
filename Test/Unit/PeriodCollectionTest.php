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


    /**
     * @test
     */
    public function subtract(){
        $collection = $this->getWorkingDayPeriod();
        $newCollection = $collection->subtract($this->getPeriod("08:21", "18:13"));

        $this->assertEquals($this->getDateString("03")." to ".$this->getDateString("05").",".
            $this->getDateString("07")." to ".$this->getDateString("08", "20", "59").",".
            $this->getDateString("18", "13", "01")." to ".$this->getDateString("19").",".
            $this->getDateString("20", "22")." to ".$this->getDateString("23")
        , $newCollection->toString());
    }

    /**
     * @test
     */
    public function intersectByPeriod(){
        $collection = $this->getWorkingDayPeriod();
        $newCollection = $collection->intersectByPeriod($this->getPeriod("08:21", "18:13"));

        $this->assertEquals($this->getDateString("08", "21")." to ".$this->getDateString("09").",".
            $this->getDateString("11")." to ".$this->getDateString("13", "35").",".
            $this->getDateString("14", "00")." to ".$this->getDateString("14", "20").",".
            $this->getDateString("16", "47")." to ".$this->getDateString("18", "13")
            , $newCollection->toString());
    }

    /**
     * @test
     */
    public function subtractCollection(){
        $collection = $this->getWorkingDayPeriod();

        $subCollection = new PeriodCollection();
        $subCollection->append($this->getPeriod("08:21", "18:13"));
        $subCollection->append($this->getPeriod("18:45", "21:16"));

        $newCollection = $collection->subtractCollection($subCollection);

        $this->assertEquals($this->getDateString("03")." to ".$this->getDateString("05").",".
            $this->getDateString("07")." to ".$this->getDateString("08", "20", "59").",".
            $this->getDateString("18", "13", "01")." to ".$this->getDateString("18", "44", "59").",".
            $this->getDateString("21", "16", "01")." to ".$this->getDateString("23")
            , $newCollection->toString());
    }

    /**
     * @test
     */
    public function intersectCollection(){
        $collection = $this->getWorkingDayPeriod();

        $subCollection = new PeriodCollection();
        $subCollection->append($this->getPeriod("08:21", "18:13"));
        $subCollection->append($this->getPeriod("18:45", "21:16"));

        $newCollection = $collection->intersectCollection($subCollection);

        $this->assertEquals($this->getDateString("08", "21")." to ".$this->getDateString("09").",".
            $this->getDateString("11")." to ".$this->getDateString("13", "35").",".
            $this->getDateString("14", "00")." to ".$this->getDateString("14", "20").",".
            $this->getDateString("16", "47")." to ".$this->getDateString("18", "13").",".
            $this->getDateString("18", "45")." to ".$this->getDateString("19").",".
            $this->getDateString("20", "22")." to ".$this->getDateString("21", "16")
            , $newCollection->toString());
    }

    /**
     * @test
     */
    public function elapsedSeconds(){
        $collection = new PeriodCollection();
        $collection->append($this->getPeriod("08:21", "18:13"));
        $collection->append($this->getPeriod("18:45", "21:16"));
        $this->assertEquals(44580, $collection->getElapsedSeconds());
    }

    /**
     * @test
     */
    public function duration(){
        $collection = new PeriodCollection();
        $collection->append($this->getPeriod("08:21", "18:13"));
        $collection->append($this->getPeriod("18:45", "21:16"));
        $this->assertEquals("12 hours 23 minutes", $collection->getDuration()->toHuman());
    }

    /**
     *
     */
    private function getWorkingDayPeriod(){
        $collection = new PeriodCollection();
        $collection->append($this->getPeriod('03:00', '05:00'));
        $collection->append($this->getPeriod('07:00', '09:00'));
        $collection->append($this->getPeriod('11:00', '13:35'));
        $collection->append($this->getPeriod('14:00', '14:20'));
        $collection->append($this->getPeriod('16:47', '19:00'));
        $collection->append($this->getPeriod('20:22', '23:00'));
        return $collection;
    }

    /**
     *
     * @param string $hourFrom
     * @param string $hourTo
     */
    private function getPeriod($hourFrom, $hourTo){
        return new Period("2012-05-05 {$hourFrom}:00", "2012-05-05 {$hourTo}:00");
    }

    /**
     *
     * @param unknown_type $time
     * @return string
     */
    private function getDateString($hour, $minute = '00', $seconds = '00'){
        return "2012-05-05 {$hour}:{$minute}:{$seconds}";
    }


}


