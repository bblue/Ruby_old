<?php
namespace Lib\Boot;
use Lib\Exceptions\DispatcherException;

use Lib\ControllerACL;
use Lib\ViewACL;
use View\ViewFactory;
use Controllers\ControllerFactory;
use Model\ServiceFactory;
use View\AbstractView;
use Lib\AbstractFactory;
use Controllers\AbstractController;
use Lib\AccessControlList;
use Lib\SecureContainer;

final class Dispatcher
{
	private $serviceFactory;
	private $controllerFactory;
	private $viewFactory;

	private $request;
	
	private $iMaxCount = 3;
	private $_count;
	
	public function __construct(ServiceFactory $serviceFactory, ControllerFactory $controllerFactory, ViewFactory $viewFactory)
	{
		$this->serviceFactory = $serviceFactory;
		$this->controllerFactory = $controllerFactory;
		$this->viewFactory = $viewFactory;
	}
	
	public function dispatch($sResourceName, $sCommand = null)
	{
		$this->count();
		try 
		{
			$this->dispatchToController($this->controllerFactory->build($sResourceName, true), $sCommand);
			$this->dispatchToView($this->viewFactory->build($sResourceName, true), $sCommand);
			return true;	
		}
		catch (\ForcedLoginException $e)
		{
			return $this->dispatch('login');
		}
		catch (DispatcherException $e)
		{
			trigger_error($e->getMessage(), E_USER_ERROR);
		}
	}
	
	private function dispatchToController(AbstractController $controller, $sCommand)
	{
		$sCommand = ($sCommand) ? $sCommand : $controller->DEFAULT_ACTION;
		$this->secureController($controller)->$sCommand();
		
		$recognition = $this->serviceFactory->build('recognition');
		$visitor = $recognition->getCurrentVisitor();
		$recognition->registerVisitor($visitor);	
	}
	
	private function dispatchToView(AbstractView $view, $sCommand)
	{
		$sCommand = ($sCommand) ? $sCommand : $view->DEFAULT_ACTION;
		return $this->secureView($view)->$sCommand();
	}
	
	private function secureController($controller)
	{
		$recognition = $this->serviceFactory->build('Recognition');
		
		$acl = new ControllerACL($recognition->getCurrentVisitor());

		return new SecureContainer($controller, $acl);
	}
	
	private function secureView($view)
	{
		$recognition = $this->serviceFactory->build('Recognition');
		
		$acl = new ViewACL($recognition->getCurrentVisitor());
		
		return new SecureContainer($view, $acl);
	}
	
	private function count()
	{
		if($this->_count++ > $this->iMaxCount)
		{
			throw new DispatcherException('Loop in dispatcher');
		}
	}
}