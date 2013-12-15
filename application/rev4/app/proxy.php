<?php
namespace App;

abstract class Proxy
{
	protected $_mapper;
	protected $_aCriterias;
	protected $_aInjectedClauses;
	
	/**
	 * Constructor
	 */
	public function __construct(AbstractDataMapper $mapper, $aCriterias, $aInjectedClauses = array())
	{
		if (!is_array($aCriterias) || empty($aCriterias)) {
			throw new \InvalidArgumentException('The mapper parameters are invalid.');
		}
		$this->_mapper = $mapper;
		$this->_aCriterias = $aCriterias; 
		$this->_aInjectedClauses = $aInjectedClauses;
	}
}