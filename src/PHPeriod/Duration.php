<?php

namespace PHPeriod;

/**
 *
 * @author chente
 *
 */
class Duration
{

    /**
     *
     * @var int
     */
    private $seconds;

    /**
     *
     * @var int
     */
    private $secondsPart = null;

    /**
     *
     * @var int
     */
    private $minutesPart = null;

    /**
     *
     * @var int
     */
    private $hoursPart = null;

    /**
     *
     * @var int
     */
    private $daysPart = null;

    /**
     *
     * @param int $seconds
     */
    public function __construct($seconds){
        $this->seconds = $seconds;
    }

    /**
     *
     */
    private function calculate(){
        $this->daysPart = floor($this->seconds / ( 24 * 3600 ) );
        $this->hoursPart = floor($this->seconds / 3600 ) - ( $this->daysPart * 24 );
        $this->minutesPart = floor($this->seconds / 60 ) - ( $this->hoursPart * 60 ) - ( $this->daysPart * 24 * 60 );
        $this->secondsPart = $this->seconds - ( $this->hoursPart * 3600 ) - ( $this->daysPart * 24 * 3600 ) - ( $this->minutesPart * 60 );
    }

    /**
     * @return int
     */
    public function getSeconds(){
        return $this->seconds;
    }

    /**
     *
     * @return int
     */
    public function getSecondsPart(){
        if( null === $this->secondsPart ){
            $this->calculate();
        }
        return $this->secondsPart;
    }

    /**
     *
     * @return int
     */
    public function getHoursPart(){
        if( null === $this->hoursPart ){
            $this->calculate();
        }
        return $this->hoursPart;
    }

    /**
     *
     * @return int
     */
    public function getMinutesPart(){
        if( null === $this->minutesPart ){
            $this->calculate();
        }
        return $this->minutesPart;
    }

    /**
     *
     * @return int
     */
    public function getDaysPart(){
        if( null === $this->daysPart ){
            $this->calculate();
        }
        return $this->daysPart;
    }

    /**
     *
     * @return string
     */
    public function toHuman(){
        $format = "%d days %d hours %d minutes %d seconds";
        $str = str_replace(array(
            ' 0 hours',
            ' 0 minutes',
            ' 0 seconds',
        ), "", sprintf($format, $this->getDaysPart(), $this->getHoursPart(), $this->getMinutesPart(), $this->getSecondsPart()));
        $str = preg_replace("/^0 days /", "", $str);
        if( strlen($str) == 0 ){
            $str = "None";
        }
        return $str;
    }

}