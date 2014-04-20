<?php

namespace App\Boot;

use Model\Services\Routing;

use App\Factories\Service as ServiceFactory;
use App\Factories\Listener as ListenerFactory;

final class FrontController
{
	private $serviceFactory;
	private $dispatcher;
	private $routingService;

	public function __construct(Dispatcher $dispatcher, Routing $routingService, ServiceFactory $serviceFactory)
	{
		$this->dispatcher = $dispatcher;
		$this->routingService = $routingService;
		$this->serviceFactory = $serviceFactory;
	}

	public function run(Request $request)
	{
		try {
			// Perform routing
			$this->routingService->route($request);

			// Authorize user for selected route
			$recognition = $this->serviceFactory->build('recognition', true);

			// Dispatch to whatever route we ended up with
			$this->dispatcher->dispatch($this->routingService->route);

		} catch (\Exception $e) {

			$sMessage = $e->getMessage();
			if(defined('DEV_AREA_CONFIRMED') && DEV_AREA_CONFIRMED === true && PRINT_EXCEPTIONS_TRACE === true) {
			    $sMessage .= '<pre>'.$e->getTraceAsString().'</pre>';
			}

		    $this->serviceFactory
    		    ->build('logging', true)
    		    ->createLogEntry($sMessage, $recognition->getCurrentVisitor(), 'danger');

		    $router = $this->routingService;
		    $router->redirect($router::ERROR_500_URL);
			$this->dispatcher->dispatch($router->route);
		}
	}
}