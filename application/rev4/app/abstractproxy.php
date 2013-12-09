<?php
namespace App;

abstract class AbstractProxy
{
    protected $_mapper;
    protected $_aCriterias;
   
    /**
     * Constructor
     */
    public function __construct(AbstractDataMapper $mapper, $aCriterias)
    {
        if (!is_array($aCriterias) || empty($aCriterias)) {
            throw new \InvalidArgumentException('The mapper parameters are invalid.');
        }
        $this->_mapper = $mapper;
        $this->_aCriterias = $aCriterias; 
    }      
}