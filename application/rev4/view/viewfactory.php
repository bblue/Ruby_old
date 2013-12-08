<?php
namespace View;

use Lib\Boot\Request;

use
	Lib\AbstractFactory,
	Model\ServiceFactory;

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