<?php

namespace Listeners;

use App\Factories\Service as ServiceFactory;

abstract class AbstractListener
{
    protected $_data = array();

    protected $serviceFactory;

    public function inject($sParam, $mParam)
    {
        $this->_data[$sParam] = $mParam;
        return $this;
    }

    public function __construct(ServiceFactory $serviceFactory)
    {
        $this->serviceFactory = $serviceFactory;
    }
}