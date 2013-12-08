<?php
namespace Controllers;

use Lib\Boot\Request;

use Model\ServiceFactory;

abstract class AbstractController
{
	protected $serviceFactory;
	protected $request;

	public $DEFAULT_ACTION = 'indexAction';
	
	public function __construct(ServiceFactory $serviceFactory, Request $request)
	{
		$this->serviceFactory = $serviceFactory;
		$this->request = $request;
	}
	
	public function indexAction()
	{
		throw new \Exception('Unable to identify index action called by ' . get_called_class());
	}
	
	public function login()
	{
		$this->serviceFactory
			->build('recognition')
			->authenticate($this->request->username, $this->request->password);
	}
	
	public function logout()
	{
		$recognition = $this->serviceFactory->build('recognition');
		$recognition->logoutVisitor($recognition->getCurrentVisitor());
	}
}