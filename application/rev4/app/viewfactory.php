<?php
namespace App;

use App\Boot\Request;

final class ViewFactory extends AbstractFactory
{
	private $serviceFactory;
	private $request;
	
	public function __construct(ServiceFactory $serviceFactory, Request $request)
	{
		$this->serviceFactory = $serviceFactory;
		$this->request = $request;
	}
	
	protected function construct($sViewName)
	{
		$sViewName = '\\View\\Views\\' . $sViewName;
		return ((!class_exists($sViewName)) ? false : new $sViewName($this->serviceFactory, $this->request));
	}
}