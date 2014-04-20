<?php
namespace Model\Services;

use App\ServiceAbstract;
use App\Event as Event;

final class EventHandler extends ServiceAbstract
{
    private $aListeners = array();

    public function addListener($sEvent, $callback)
    {
        $this->aListeners[$sEvent][] = $callback;
    }

    public function dispatch($sEvent, Event $event = null)
    {
        if($event === null) {
            $event = $this->buildEvent();
        }

        $event->setDispatcher($this);

        $this->doDispatch($this->getListeners($sEvent), $sEvent, $event);

        return $event;
    }

    private function getListeners($sEvent)
    {
        if(empty($this->aListeners)) {
            return array();
        }

        if(!array_key_exists($sEvent, $this->aListeners)) {
            return array();
        }

        return $this->aListeners[$sEvent];
    }

    public function buildEvent(array $aParameters = array())
    {
        $event = new Event();
        foreach($aParameters as $sParameterName => $mValue) {
            $event->$sParameterName = $mValue;
        }
        return $event;
    }


    /**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[] $listeners The event listeners.
     * @param string $eventName The name of the event to dispatch.
     * @param Event $event The event object to pass to the event handlers/listeners.
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            call_user_func($listener, $event, $eventName, $this);
        }
    }
}