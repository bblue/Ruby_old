<?php
namespace App;

use App\Boot\Request;
use App\Factory;

abstract class AbstractController
{
	protected $serviceFactory;
	protected $request;
	
	public function __construct(Factory $serviceFactory, Request $request)
	{
		$this->serviceFactory = $serviceFactory;
		$this->request = $request;
	}
	
	public function indexAction()
	{
		return true;
	}
	
	public function registerCurrentVisitor()
	{
		$recognition = $this->serviceFactory->build('recognition', true);
		$recognition->registerVisitor($recognition->getCurrentVisitor());
	}
}