<?php

namespace PHPeriod;

/**
 *
 * PeriodCollection
 *
 * @category Application\Model\PeriodCollection
 * @author chente
 */
class PeriodCollection extends \ArrayIterator
{

    /**
     *
     *
     * @return PeriodCollection
     */
    public function newInstance(){
        return new static();
    }

    /**
     *
     * validate period
     * @param Period $period
     */
    protected function validate($period)
    {
        if( !($period instanceof Period) ){
            throw new \InvalidArgumentException("Debe de cumplir con la Interface Period");
        }
    }

    /**
     *
     * validate Callback
     * @param callable $callable
     * @throws \InvalidArgumentException
     */
    protected function validateCallback($callable)
    {
        if( !is_callable($callable) ){
            throw new \InvalidArgumentException("Is not a callable function");
        }
    }

    /**
     * Appends the value
     * @param Period $period
     */
    public function append($period)
    {
        $this->validate($period);
        parent::offsetSet($period->getIndex(), $period);
        $this->rewind();
    }

    /**
     * Return current array entry
     * @return Period
     */
    public function current()
    {
        return parent::current();
    }

    /**
     * Return current array entry and
     * move to next entry
     * @return Period
     */
    public function read()
    {
        $period = $this->current();
        $this->next();
        return $period;
    }

    /**
     * Get the first array entry
     * if exists or null if not
     * @return Period|null
     */
    public function getOne()
    {
        if ($this->count() > 0)
        {
            $this->seek(0);
            return $this->current();
        } else
            return null;
    }

    /**
     * Contains one period with $name
     * @param  int $index
     * @return boolean
     */
    public function containsIndex($index)
    {
        return parent::offsetExists($index);
    }

    /**
     *
     * @param array $array
     * @return boolean
     */
    public function containsAll($ids)
    {
        if( $this->isEmpty() || empty($ids) ){
            return false;
        }

        $containsAll = true;
        foreach( $ids as $index ){
            $containsAll = $containsAll && $this->containsIndex($index);
            if( false === $containsAll ){
                break;
            }
        }
        return $containsAll;
    }

    /**
     *
     * @param array $ids
     * @return boolean
     */
    public function containsAny($ids)
    {
        if( $this->isEmpty() || empty($ids) ){
            return false;
        }

        foreach( $ids as $index ){
            if( $this->containsIndex($index) ){
                return true;
            }
        }
        return false;
    }

    /**
     * Remove one period with $name
     * @param  int $index
     */
    public function remove($index)
    {
        if( $this->containsIndex($index) )
            $this->offsetUnset($index);
    }

    /**
     * Merge two PeriodCollection
     * @param PeriodCollection $periodCollection
     * @return PeriodCollection
     */
    public function merge(PeriodCollection $periodCollection)
    {
        $newPeriodCollection = $this->copy();
        $periodCollection->each($this->appendFunction($newPeriodCollection));
        return $newPeriodCollection;
    }

    /**
     * @return PeriodCollection
     */
    public function copy()
    {
        $newPeriodCollection = $this->newInstance();
        $this->each($this->appendFunction($newPeriodCollection));
        return $newPeriodCollection;
    }

    /**
     * Diff two PeriodCollections
     * @param PeriodCollection $periodCollection
     * @return PeriodCollection
     */
    public function diff(PeriodCollection $periodCollection)
    {
        $newPeriodCollection = $this->newInstance();
        $this->each(function(Period $period) use($newPeriodCollection, $periodCollection){
            if( !$periodCollection->containsIndex($period->getIndex()) ){
                $newPeriodCollection->append($period);
            }
        });
        return $newPeriodCollection;
    }

    /**
     * Intersect two PeriodCollection
     * @param PeriodCollection $periodCollection
     * @return PeriodCollection
     */
    public function intersect(PeriodCollection $periodCollection)
    {
        $newPeriodCollection = $this->newInstance();
        $this->each(function(Period $period) use($newPeriodCollection, $periodCollection){
            if( $periodCollection->containsIndex($period->getIndex()) ){
                $newPeriodCollection->append($period);
            }
        });
        return $newPeriodCollection;
    }

    /**
     * Retrieve the array with primary keys
     * @return array
     */
    public function getPrimaryKeys()
    {
        return array_keys($this->getArrayCopy());
    }

    /**
     * Retrieve the Period with primary key
     * @param  int $name
     * @return Period
     */
    public function getByPK($index)
    {
        return $this->containsIndex($index) ? $this[$index] : null;
    }

    /**
     * Is Empty
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->count() == 0;
    }

    /**
     *
     * @param \Closure $callable
     */
    public function each($callable)
    {
        $this->validateCallback($callable);

        $this->rewind();
        while( $this->valid() )
        {
            $period = $this->read();
            $callable($period);
        }
        $this->rewind();
    }

    /**
     *
     * @param \Closure $callable
     * @return array
     */
    public function map($callable)
    {
        $this->validateCallback($callable);

        $array = array();
        $this->each(function(Period $period) use(&$array, $callable){
            $mapResult = $callable($period);
            if( is_array($mapResult) ){
                foreach($mapResult as $key => $value){
                    $array[$key] = $value;
                }
            }else{
                $array[] = $mapResult;
            }
        });

        return $array;
    }

    /**
     *
     * @param \Closure $callable
     * @return PeriodCollection
     */
    public function filter($callable)
    {
        $this->validateCallback($callable);

        $newPeriodCollection = $this->newInstance();
        $this->each(function(Period $period) use($newPeriodCollection, $callable){
            if( $callable($period) ){
                $newPeriodCollection->append($period);
            }
        });

        return $newPeriodCollection;
    }

    /**
     * @param mixed $start
     * @param callable $callable
     * @return mixed
     */
    public function foldLeft($start, $callable)
    {
        $this->validateCallback($callable);
        $result = $start;
        $this->each(function(Period $period) use(&$result, $callable){
            $result = $callable($result, $period);
        });
        return $result;
    }

    /**
     *
     * @param callable $callable
     * @return boolean
     */
    public function forall($callable)
    {
        if( $this->isEmpty() ) return false;
        $this->validateCallback($callable);
        return $this->foldLeft(true, function($boolean, Period $period) use($callable){
            return $boolean && $callable($period);
        });
    }

    /**
     *
     * @param callable $callable
     * @return array
     */
    public function partition($callable)
    {
        $this->validateCallback($callable);

        $periodCollections = array();
        $getPeriodCollection = $this->periodCollectionGenerator($periodCollections);
        $this->each(function(Period $period) use($getPeriodCollection, $callable){
            $getPeriodCollection($callable($period))->append($period);
        });

        return $periodCollections;
    }

    /**
     * convert to array
     * @return string
     */
    public function toString(){
        return implode($this->map(function(Period $period){
            return array($period->getIndex() => $period->toString());
        }), ",");
    }

    /**
     *
     * @param Period $period
     * @return PeriodCollection
     */
    public function subtract(Period $subPeriod){
        $newCollection = $this->newInstance();
        $this->each(function(Period $period) use(&$newCollection, $subPeriod){
            $newCollection = $newCollection->merge($period->subtract($subPeriod));
        });

        return $newCollection;
    }

    /**
     *
     * @param Period $period
     * @return PeriodCollection
     */
    public function intersectByPeriod(Period $subPeriod){
        $newCollection = $this->newInstance();
        $this->each(function(Period $period) use(&$newCollection, $subPeriod){
            $newCollection = $newCollection->merge($period->intersect($subPeriod));
        });

        return $newCollection;
    }

    /**
     *
     * @param PeriodCollection $periodCollection
     * @return PeriodCollection
     */
    public function subtractCollection(PeriodCollection $periodCollection)
    {
        $self = $this;
        while( $periodCollection->valid() ) {
            $subPeriod = $periodCollection->read();
            $self = $self->subtract($subPeriod);
        }
        $periodCollection->rewind();

        return $self;
    }

    /**
     *
     * @param PeriodCollection $periodCollection
     * @return PeriodCollection
     */
    public function intersectCollection(PeriodCollection $periodCollection)
    {
        $newCollection = $this->newInstance();

        while( $periodCollection->valid() ) {
            $subPeriod = $periodCollection->read();
            $newCollection = $newCollection->merge($this->intersectByPeriod($subPeriod));
        }
        $periodCollection->rewind();
        $newCollection->rewind();

        return $newCollection;
    }

    /**
     * @return int
     */
    public function getElapsedSeconds(){
        return $this->foldLeft(0, function($acc, Period $period){
            return $acc + $period->getElapsedSeconds();
        });
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
     * @param int $seconds
     * @return \PHPeriod\PeriodCollection
     */
    public function truncate($seconds)
    {
        $collection = $this->newInstance();
        $currentSeconds = 0;

        $this->rewind();
        while( $this->valid() ) {
            $period = $this->read();

            if( ( $period->getElapsedSeconds() + $currentSeconds ) <= $seconds ){
                $currentSeconds += $period->getElapsedSeconds();
                $collection->append($period);
            }else{
                $endDate = clone $period->getStartDate();
                $seg = $seconds - $currentSeconds;
                $endDate->add(new \DateInterval("PT{$seg}S"));
                $collection->append(new Period($period->getStartDate()->format(Period::MYSQL_FORMAT), $endDate->format(Period::MYSQL_FORMAT)));
                break;
            }

        }
        $this->rewind();
        $collection->rewind();

        return $collection;
    }

    /**
     * @return \DateTime
     * @throws Exception
     */
    public function getStartDate(){
        if( $this->isEmpty() ){
            throw new Exception("El periodo esta vacio");
        }
        return $this->getOne()->getStartDate();
    }

    /**
     * @return \DateTime
     * @throws Exception
     */
    public function getEndDate(){
        if( $this->isEmpty() ){
            throw new Exception("El periodo esta vacio");
        }
        $last = $this->getLast();
        if( null == $last ){
            throw new Exception("No se pudo encontrar el ultimo elemento");
        }
        return $last->getEndDate();
    }

    /**
     *
     * @return \PHPeriod\Period
     */
    public function getLast(){
        $keys = $this->getPrimaryKeys();
        $key = array_pop($keys);
        return $this->getByPK($key);
    }

    /**
     *
     * @param array $periodCollections
     * @return \Closure
     */
    private function periodCollectionGenerator(array & $periodCollections){
        $self = $this;
        $getPeriodCollection = function($index) use(&$periodCollections, $self){
            if( !isset($periodCollections[$index]) ){
                $periodCollections[$index] = $self->newInstance();
            }
            return $periodCollections[$index];
        };
        return $getPeriodCollection;
    }

    /**
     *
     * @param PeriodCollection $newCollenction
     * @return \Closure
     */
    private function appendFunction($newPeriodCollection){
        $appendFunction = function(Period $period) use($newPeriodCollection){
            if( !$newPeriodCollection->containsIndex( $period->getIndex() ) ){
                $newPeriodCollection->append($period);
            }
        };
        return $appendFunction;
    }

}