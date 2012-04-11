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
    private $startDate;

    /**
     *
     * @var \Zend_Date
     */
    private $endDate;

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
     * @param \Zend_Date $endDate
     */
    private function setEndDate(\Zend_Date $endDate){
        $this->endDate = clone $endDate;
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
     * @return string
     */
    public function getIndex(){
        return "{$this->startDate->get('yyyy-MM-dd HH:mm:ss')} to {$this->endDate->get('yyyy-MM-dd HH:mm:ss')}";
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