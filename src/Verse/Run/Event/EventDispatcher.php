<?php


namespace Run\Event;


use Run\Event\Object\EventObjectProto;

class EventDispatcher
{
    private $subscribers        = [];
    private $subscriberEvents   = [];
    private $eventSubscriptions = [];
    
    private $subscriberId = 0;
    
    public function dispatch(EventObjectProto $eventObject)
    {
        $eventId = $eventObject->getId();
        
        if (empty($this->eventSubscriptions[$eventId])) {
            return false;
        }
        
        foreach ($this->eventSubscriptions[$eventId] as $subscriberId => $callable) {
            call_user_func($callable, $eventObject, $this);       
        }
    }
    
    /**
     * Подписаться на системное событие
     *
     * @param $callable         callable
     * @param $eventId          integer
     * @param $subscriberName   string class or method
     *
     * @return string
     *
     */
    public function subscribe($callable, $eventId, $subscriberName)
    {
        
        $subscriberId = $this->_registerSubscriber($subscriberName);
        
        $this->eventSubscriptions[$eventId][$subscriberId] = $this->subscriberId;
        $this->subscriberEvents[$subscriberId][$eventId]   = $callable;
        
        return $subscriberId;
    }
    
    public function subscribeMass($callableByEventArray, $subscriberName)
    {
        foreach ($callableByEventArray as $callableByEvent) {
            list($event, $callable) = $callableByEvent;
            $this->subscribe($callable, $event, $subscriberName);
        }
    }
    
    private function _registerSubscriber($subscriberName)
    {
        $this->subscriberId++;
        if (isset($this->subscribers[$subscriberName])) {
            
        }
        
        return $subscriberName ?: 'Sub.' . $this->subscriberId;
    }
    
    public function unsubscribeEvents($subscriberName, $events)
    {
        foreach ($events as $event) {
            if (isset($this->subscriberEvents[$subscriberName][$event])) {
                unset($this->eventSubscriptions[$event][$subscriberName], $this->subscriberEvents[$subscriberName][$event]);
            }
        }
        
        if (empty($this->subscriberEvents[$subscriberName])) {
            unset($this->subscriberEvents[$subscriberName], $this->subscribers[$subscriberName]);
        }
    }
    
    public function unsubscribeAll($subscriberName)
    {
        if (empty($this->subscriberEvents[$subscriberName])) {
            return;
        }
        
        foreach ($this->subscriberEvents[$subscriberName] as $event) {
            unset($this->eventSubscriptions[$event][$subscriberName]);
        }
        
        unset($this->subscriberEvents[$subscriberName], $this->subscribers[$subscriberName]);
    }
}