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
        return new Period(new \Zend_Date("2012-05-05 {$hourFrom}:00", 'yyyy-MM-dd HH:mm:ss'), new \Zend_Date("2012-05-05 {$hourTo}:00", 'yyyy-MM-dd HH:mm:ss'));
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


