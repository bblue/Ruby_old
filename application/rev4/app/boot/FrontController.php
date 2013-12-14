<?php

namespace App\Boot;

use Model\Services\Routing;

use App\Factories\Controller as ControllerFactory,
	App\Factories\View as ViewFactory,
	App\Factories\Service as ServiceFactory;

final class FrontController
{
	private $serviceFactory;
	private $controllerFactory;
	private $viewFactory;
	
	public function __construct(Dispatcher $dispatcher, ServiceFactory $serviceFactory)
	{
		$this->dispatcher = $dispatcher;
		$this->serviceFactory = $serviceFactory;
	}
	
	public function run(Request $request)
	{
		$visitor 	= $this->serviceFactory->build('recognition')->getCurrentVisitor();
		$routing 	= $this->serviceFactory->build('routing');
		$acl 		= $this->serviceFactory->build('acl');
		
		// Run the requested route via the routing mechanism, and check it towards the ACL
		$route = $routing->route($request->getResourceName(), $visitor, $acl); 

		// Dispatch to whatever route we ended up with
		$this->dispatcher->dispatch($route, $request);
		
		// Clear the response log before next 
		$this->serviceFactory->build('model')->clearModelResponse();
	}
}