<?php

namespace PHPeriod;

/**
 *
 * @author chente
 *
 */
class Period
{

    /**
     *
     * @var unknown_type
     */
    const MYSQL_FORMAT = 'yyyy-MM-dd HH:mm:ss';

    /**
     *
     * @var \Zend_Date
     */
    protected $startDate;

    /**
     *
     * @var \Zend_Date
     */
    protected $endDate;

    /**
     *
     * @var string
     */
    protected $format;

    /**
     *
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     * @param string $format
     */
    public function __construct($startDate, $endDate, $format = self::MYSQL_FORMAT){
        $this->format = $format;
        $startDate = $this->makeDate($startDate);
        $endDate = $this->makeDate($endDate);
        $this->checkRange($startDate, $endDate);
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }

    /**
     *
     * @param string $strDate
     * @return \Zend_Date
     */
    protected function makeDate($strDate){
        if( !\Zend_Date::isDate($strDate, $this->format) ){
            throw new Exception("The date {$strDate} with format {$this->format} is invalid");
        }
        return new \Zend_Date($strDate, $this->format);
    }

    /**
     *
     * @return string
     */
    public function toString(){
        return "{$this->startDate->get($this->format)} to {$this->endDate->get($this->format)}";
    }

    /**
     * @return string
     */
    public function getIndex(){
       return $this->toString();
    }

    /**
     *
     * @param string $startDate
     */
    public function adjustStartDate($startDate){
        $startDate = $this->makeDate($startDate);
        $this->checkRange($startDate, $this->endDate);
        $this->setStartDate($startDate);
    }

    /**
     *
     * @param string $endDate
     */
    public function adjustEndDate($endDate){
        $endDate = $this->makeDate($endDate);
        $this->checkRange($this->startDate, $endDate);
        $this->setEndDate($endDate);
    }

    /**
     *
     * @param \Zend_Date $startDate
     */
    private function setStartDate(\Zend_Date $startDate){
        $this->startDate = $startDate;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getStartDate(){
        return $this->startDate;
    }

    /**
     *
     * @param \Zend_Date $endDate
     */
    private function setEndDate(\Zend_Date $endDate){
        $this->endDate = $endDate;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getEndDate(){
        return $this->endDate;
    }

    /**
     *
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     */
    public function adjust($startDate, $endDate){
        $startDate = $this->makeDate($startDate);
        $endDate = $this->makeDate($endDate);
        $this->checkRange($startDate, $endDate);
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }

    /**
     *
     * @return \PHPeriod\PeriodCollection
     */
    public function toCollection(){
        $collection = new PeriodCollection();
        $collection->append($this);
        return $collection;
    }

    /**
     *
     * @param Period $period
     * @return boolean
     */
    public function isInside(Period $period){
        return self::isGreaterOrEqual($period->getStartDate(), $this->getStartDate()) &&
        self::isLowerOrEqual($period->getEndDate(), $this->getEndDate());
    }

    /**
     *
     * @param Period $period
     * @return boolean
     */
    public function isCoveringTo(Period $period){
       return $period->isInside($this);
    }

    /**
     *
     * @param Period $period
     * @return boolean
     */
    public function isLeftSideFrom(Period $period){
        return self::isLower($period->getStartDate(), $this->getEndDate());
    }

    /**
     *
     * @param Period $period
     * @return boolean
     */
    public function isPartiallyLeftSide(Period $period){
        return self::isLower($period->getStartDate(), $this->getStartDate())
        && self::isGreaterOrEqual($period->getStartDate(), $this->getEndDate())
        && self::isLower($period->getEndDate(), $this->getEndDate());
    }

    /**
     *
     * @param Period $period
     * @return boolean
     */
    public function isRightSideFrom(Period $period){
        return self::isGreater($period->getEndDate(), $this->getStartDate());
    }

    /**
     *
     * @param Period $period
     * @return boolean
     */
    public function isPartiallyRigthSide(Period $period){
        return self::isLowerOrEqual($period->getEndDate(), $this->getStartDate())
        && self::isGreater($period->getEndDate(), $this->getEndDate())
        && self::isGreater($period->getStartDate(), $this->getStartDate());
    }


    /**
     *
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     * @return boolean
     */
    public static function isGreaterOrEqual(\Zend_Date $startDate, \Zend_Date $endDate, $format = self::MYSQL_FORMAT){
        return $startDate->compare($endDate->get($format), $format) <= 0;
    }

    /**
     *
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     * @return boolean
     */
    public static function isLowerOrEqual(\Zend_Date $startDate, \Zend_Date $endDate, $format = self::MYSQL_FORMAT){
        return $startDate->compare($endDate->get($format), $format) >= 0;
    }

    /**
     *
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     * @return boolean
     */
    public static function isGreater(\Zend_Date $startDate, \Zend_Date $endDate, $format = self::MYSQL_FORMAT){
        return $startDate->compare($endDate->get($format), $format) < 0;
    }

    /**
     *
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     * @return boolean
     */
    public static function isLower(\Zend_Date $startDate, \Zend_Date $endDate, $format = self::MYSQL_FORMAT){
        return $startDate->compare($endDate->get($format), $format) > 0;
    }

    /**
     *
     * @param Period $period
     * @return \PHPeriod\PeriodCollection
     */
    public function subtract(Period $period){
        $collection = new PeriodCollection();

        if( $period->isLeftSideFrom($this) || $period->isRightSideFrom($this) ){
            $collection->append($this);
        }else if( $period->isPartiallyLeftSide($this) ){
            $startDate = clone $period->getEndDate();
            $startDate->addSecond(1);
            $collection->append(new Period($startDate->get($this->format), $this->getEndDate()->get($this->format)));
        }else if( $period->isPartiallyRigthSide($this) ){
            $endDate = clone $period->getStartDate();
            $endDate->subSecond(1);
            $collection->append(new Period($this->getStartDate()->get($this->format), $endDate->get($this->format)));
        }elseif ( $period->isInside($this) ){
            $endDate = clone $period->getStartDate();
            $endDate->subSecond(1);
            $periodOne = new Period($this->getStartDate()->get($this->format), $endDate->get($this->format));
            $collection->append($periodOne);

            $startDate = clone $period->getEndDate();
            $startDate->addSecond(1);
            $periodTwo = new Period($startDate->get($this->format), $this->getEndDate()->get($this->format));
            $collection->append($periodTwo);
        }

        return $collection;
    }

    /**
     * @return int
     */
    public function getElapsedSeconds(){
        return $this->getEndDate()->get('U') - $this->getStartDate()->get('U');
    }

    /**
     *
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     * @throws Exception
     */
    private function checkRange(\Zend_Date $startDate, \Zend_Date $endDate){
        if( $endDate->isEarlier($startDate->get($this->format), $this->format) ){
            throw new Exception("The period is invalid");
        }
    }

}