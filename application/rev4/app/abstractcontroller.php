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
		throw new \Exception('Unable to identify index action called by ' . get_called_class());
	}
	
	public function registerCurrentVisitor()
	{
		$recognition = $this->serviceFactory->build('recognition');
		$recognition->registerVisitor($recognition->getCurrentVisitor());
	}
	
	public function login()
	{
		$this->serviceFactory
			->build('recognition')
			->authenticate($this->request->username, $this->request->password);
		return true;
	}
	
	public function logout()
	{
		$recognition = $this->serviceFactory->build('recognition');
		$recognition->logoutVisitor($recognition->getCurrentVisitor());
		return true;
	}
}