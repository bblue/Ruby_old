<?php
namespace App\Boot;

use
	App\Factories\View 			as ViewFactory,
	App\Factories\Controller	as ControllerFactory;

use Model\Domain\Route\Route;

final class Dispatcher
{
	private $controllerFactory;
	private $viewFactory;
	
	public function setControllerFactory(ControllerFactory $controllerFactory)
	{
		$this->controllerFactory = $controllerFactory;
	}	

	public function setViewFactory(ViewFactory $viewFactory)
	{
		$this->viewFactory = $viewFactory;
	}
	
	public function dispatch(Route $route)
	{
		$controller	= $this->controllerFactory->build($route->getResourceName());
		$view 		= $this->viewFactory->build($route->getResourceName());
		$sCommand 	= $route->getCommand();
		
		try {
			// Execute command on controller
			$controllerResponse = $controller->execute($sCommand, $view);
		} catch (Exception $e) {
		    // Catch error not caught by controller
			trigger_error($e->getMessage(), E_USER_ERROR);
		}
		
		// Execute view
		$view->execute();
	}
}