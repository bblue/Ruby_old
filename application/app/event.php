<?php
namespace App;

class Event
{
    protected $_data = array();
    private $dispatcher;

    public function __set($sParam, $mValue)
    {
        $this->_data[$sParam] = $mValue;
        return $this;
    }

    public function __get($sParam)
    {
        if(isset($this->_data[$sParam])) {
            return $this->_data[$sParam];
        } else {
            throw new \Exception($sParam . ' is unset');
        }
    }

    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getDispatcher()
    {
        if(isset($this->dispatcher)) {
            return $this->dispatcher;
        } else {
            throw new \Exception('Dispatcher is not set for event');
        }
    }
}
