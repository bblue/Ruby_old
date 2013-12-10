<?php
namespace App\Boot;

use App\Factories\Service as ServiceFactory,
	App\Factories\View as ViewFactory,
	App\Factories\Controller as ControllerFactory;

use Model\Domain\Route\Route;

final class Dispatcher implements DispatcherInterface
{
	private $serviceFactory;
	private $controllerFactory;
	private $viewFactory;

	public function setServiceFactory(ServiceFactory $serviceFactory)
	{
		$this->serviceFactory = $serviceFactory;
	}

	public function setControllerFactory(ControllerFactory $controllerFactory)
	{
		$this->controllerFactory = $controllerFactory;
	}	

	public function setViewFactory(ViewFactory $viewFactory)
	{
		$this->viewFactory = $viewFactory;
	}
	
	private function createController($sControllerName)
	{
		return $this->controllerFactory->construct($sControllerName);
	}
	
	private function createView($sViewName)
	{
		return $this->viewFactory->construct($sViewName);
	}
	
	public function dispatch(Route $route, Request $request)
	{
		$controller = $this->createController($route->getResourceName());

		$view = $this->createView($route->getResourceName());

		$sCommand = $route->getCommand();

		try 
		{
			if(!$controller->$sCommand($request))
			{
				throw new Exception('Command on controller did not execute as expected');
			}

			if(!$view->$sCommand())
			{
				throw new Exception('Command on view did not execute as expected');
			}
		}
		catch (Exception $e)
		{
			// Prepare log entry
			
			// Dispatch to another route @TODO: Dette kan skape en loop, det må jeg fikse
		}

		return true;
	}
}