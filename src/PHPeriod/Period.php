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
    const MYSQL_FORMAT = 'Y-m-d H:i:s';

    /**
     *
     * @var \DateTime
     */
    protected $startDate;

    /**
     *
     * @var \DateTime
     */
    protected $endDate;

    /**
     *
     * @var string
     */
    protected $format;

    /**
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
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
     * @return \DateTime
     */
    protected function makeDate($strDate){
        $dateTime = \DateTime::createFromFormat($this->format, $strDate);
        if( false == $dateTime ){
            throw new Exception("The date {$strDate} with format {$this->format} is invalid");
        }
        return $dateTime;
    }

    /**
     *
     * @return string
     */
    public function toString(){
        return "{$this->startDate->format($this->format)} to {$this->endDate->format($this->format)}";
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
     * @param \DateTime $startDate
     */
    private function setStartDate(\DateTime $startDate){
        $this->startDate = $startDate;
    }

    /**
     *
     * @return \DateTime
     */
    public function getStartDate(){
        return $this->startDate;
    }

    /**
     *
     * @param \DateTime $endDate
     */
    private function setEndDate(\DateTime $endDate){
        $this->endDate = $endDate;
    }

    /**
     *
     * @return \DateTime
     */
    public function getEndDate(){
        return $this->endDate;
    }

    /**
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
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
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return boolean
     */
    public static function isGreaterOrEqual(\DateTime $startDate, \DateTime $endDate, $format = self::MYSQL_FORMAT){
        return $endDate->getTimestamp() >= $startDate->getTimestamp();
        //return $startDate->compare($endDate->get($format), $format) <= 0;
    }

    /**
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return boolean
     */
    public static function isLowerOrEqual(\DateTime $startDate, \DateTime $endDate, $format = self::MYSQL_FORMAT){
        return $endDate->getTimestamp() <= $startDate->getTimestamp();
        //return $startDate->compare($endDate->get($format), $format) >= 0;
    }

    /**
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return boolean
     */
    public static function isGreater(\DateTime $startDate, \DateTime $endDate, $format = self::MYSQL_FORMAT){
        return $endDate->getTimestamp() > $startDate->getTimestamp();
    }

    /**
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return boolean
     */
    public static function isLower(\DateTime $startDate, \DateTime $endDate, $format = self::MYSQL_FORMAT){
        return $endDate->getTimestamp() < $startDate->getTimestamp();
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
            $startDate->add(new \DateInterval("PT1S"));
            $collection->append(new Period($startDate->format($this->format), $this->getEndDate()->format($this->format)));
        }else if( $period->isPartiallyRigthSide($this) ){
            $endDate = clone $period->getStartDate();
            $endDate->sub(new \DateInterval("PT1S"));
            $collection->append(new Period($this->getStartDate()->format($this->format), $endDate->format($this->format)));
        }elseif ( $period->isInside($this) ){
            $endDate = clone $period->getStartDate();
            $endDate->sub(new \DateInterval("PT1S"));
            $periodOne = new Period($this->getStartDate()->format($this->format), $endDate->format($this->format));
            $collection->append($periodOne);

            $startDate = clone $period->getEndDate();
            $startDate->add(new \DateInterval("PT1S"));
            $periodTwo = new Period($startDate->format($this->format), $this->getEndDate()->format($this->format));
            $collection->append($periodTwo);
        }

        return $collection;
    }

    /**
     *
     * @param Period $period
     * @return \PHPeriod\PeriodCollection
     */
    public function intersect(Period $period)
    {
        $collection = new PeriodCollection();

        if( $period->isCoveringTo($this) ){
            $collection->append($this);
        }else if( $period->isPartiallyLeftSide($this) ){
            $collection->append(new Period($this->getStartDate()->format($this->format), $period->getEndDate()->format($this->format)));
        }else if( $period->isPartiallyRigthSide($this) ){
            $collection->append(new Period($period->getStartDate()->format($this->format), $this->getEndDate()->format($this->format)));
        }elseif ( $period->isInside($this) ){
            $collection->append($period);
        }

        return $collection;
    }

    /**
     * @return int
     */
    public function getElapsedSeconds(){
        return $this->getEndDate()->getTimestamp() - $this->getStartDate()->getTimestamp();
    }

    /**
     *
     * @return \PHPeriod\Duration
     */
    public function getDuration(){
        return new Duration($this->getElapsedSeconds());
    }

    /**
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @throws Exception
     */
    private function checkRange(\DateTime $startDate, \DateTime $endDate){
        if( $endDate->getTimestamp() <= $startDate->getTimestamp()  ){
            throw new Exception("The period is invalid");
        }
    }

}