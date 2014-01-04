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
		$this->_mapper = $mapper;
		$this->_aCriterias = $aCriterias; 
		$this->_aInjectedClauses = $aInjectedClauses;
	}
}