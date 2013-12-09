<?php

namespace App;
use Model\Domain\Visitor\Visitor;

abstract class AccessControlList
{
	private $visitor;
	
	public function __construct(Visitor $visitor)
	{
		$this->visitor = $visitor;
	}
	
	public function isAllowed($className, $methodName)
	{
		// Check if methodname within classname can be accessed by visitor
		
		// Check forced login and throw forced login exception. Remember that forced login may not apply to some controllers

		return true;
	}
}