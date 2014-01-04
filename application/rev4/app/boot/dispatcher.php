<?php
namespace App\Boot;

use App\Factories\Service as ServiceFactory,
	App\Factories\View as ViewFactory,
	App\Factories\Controller as ControllerFactory;

use Model\Domain\Route\Route;

final class Dispatcher
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
		return $this->controllerFactory->build($sControllerName);
	}
	
	private function createView($sViewName)
	{
		return $this->viewFactory->build($sViewName);
	}
	
	public function dispatch(Route $route, Request $request)
	{
		$controller	= $this->createController($route->getResourceName());
		$view 		= $this->createView($route->getResourceName());
		$sCommand 	= $route->getCommand();
	    
		if(PRINT_CONTROLLER_COMMAND === true)
    	{
    		echo '<pre>Performing command on controller: <i>$' . $route->getResourceName() . '->' . $sCommand . '()</i></pre>';
    	}
    	
		try 
		{
			if(!is_callable(array($controller, $sCommand)))
			{
				throw new \Exception($sCommand . ' is not a recognized command on ' . get_class($controller));
			}
			
			// Execute command on controller
			if(!$controller->$sCommand($request))
			{
				throw new \Exception('Command on controller did not execute as expected');
			}

			// Register visitor
			$controller->registerCurrentVisitor();
			
			// Execute command on view
			if(!$view->execute($sCommand))
			{
				throw new \Exception('Command on view did not execute as expected');
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