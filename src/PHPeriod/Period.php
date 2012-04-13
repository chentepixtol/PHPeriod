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
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     */
    public function __construct(\Zend_Date $startDate, \Zend_Date $endDate){
        $this->checkRange($startDate, $endDate);
        $this->adjust($startDate, $endDate);
    }

    /**
     *
     * @return string
     */
    public function toString(){
        return "{$this->startDate->get('yyyy-MM-dd HH:mm:ss')} to {$this->endDate->get('yyyy-MM-dd HH:mm:ss')}";
    }

    /**
     * @return string
     */
    public function getIndex(){
       return $this->toString();
    }

    /**
     *
     * @param \Zend_Date $startDate
     */
    public function adjustStartDate(\Zend_Date $startDate){
        $this->checkRange($startDate, $this->endDate);
        $this->setStartDate($startDate);
    }

    /**
     *
     * @param \Zend_Date $endDate
     */
    public function adjustEndDate(\Zend_Date $endDate){
        $this->checkRange($this->startDate, $endDate);
        $this->setEndDate($endDate);
    }

    /**
     *
     * @param \Zend_Date $startDate
     */
    private function setStartDate(\Zend_Date $startDate){
        $this->startDate = clone $startDate;
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
        $this->endDate = clone $endDate;
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
    public function adjust(\Zend_Date $startDate, \Zend_Date $endDate){
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
    public static function isGreaterOrEqual(\Zend_Date $startDate, \Zend_Date $endDate){
        return $startDate->compare($endDate->get('yyyy-MM-dd HH:mm:ss'), 'yyyy-MM-dd HH:mm:ss') <= 0;
    }

    /**
     *
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     * @return boolean
     */
    public static function isLowerOrEqual(\Zend_Date $startDate, \Zend_Date $endDate){
        return $startDate->compare($endDate->get('yyyy-MM-dd HH:mm:ss'), 'yyyy-MM-dd HH:mm:ss') >= 0;
    }

    /**
     *
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     * @return boolean
     */
    public static function isGreater(\Zend_Date $startDate, \Zend_Date $endDate){
        return $startDate->compare($endDate->get('yyyy-MM-dd HH:mm:ss'), 'yyyy-MM-dd HH:mm:ss') < 0;
    }

    /**
     *
     * @param \Zend_Date $startDate
     * @param \Zend_Date $endDate
     * @return boolean
     */
    public static function isLower(\Zend_Date $startDate, \Zend_Date $endDate){
        return $startDate->compare($endDate->get('yyyy-MM-dd HH:mm:ss'), 'yyyy-MM-dd HH:mm:ss') > 0;
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
            $collection->append(new Period($startDate, $this->getEndDate()));
        }else if( $period->isPartiallyRigthSide($this) ){
            $endDate = clone $period->getStartDate();
            $endDate->subSecond(1);
            $collection->append(new Period($this->getStartDate(), $endDate));
        }elseif ( $period->isInside($this) ){
            $endDate = clone $period->getStartDate();
            $endDate->subSecond(1);
            $periodOne = new Period($this->getStartDate(), $endDate);
            $collection->append($periodOne);

            $startDate = clone $period->getEndDate();
            $startDate->addSecond(1);
            $periodTwo = new Period($startDate, $this->getEndDate());
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
        if( $endDate->isEarlier($startDate->get('yyyy-MM-dd HH:mm:ss'), 'yyyy-MM-dd HH:mm:ss') ){
            throw new Exception("The period is invalid");
        }
    }

}