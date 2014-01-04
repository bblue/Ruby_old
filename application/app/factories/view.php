<?php
namespace App\Factories;
use App\Factory;
use App\Boot\Request;

final class View extends Factory
{
	private $serviceFactory;
	private $request;
	
	public function __construct(Factory $serviceFactory, Request $request)
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