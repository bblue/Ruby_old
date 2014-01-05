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
		$recognition	= $this->serviceFactory->build('recognition', true);
		$visitor = $recognition->getCurrentVisitor();
		$recognition->registerVisitor($visitor);

		$routing		= $this->serviceFactory->build('routing');

		//$acl			= $this->serviceFactory->build('acl');
		
		// Run the requested route via the routing mechanism, and check it towards the ACL
		$route = $routing->route($request->getResourceName(), $visitor);
		
		/*
		// @todo: Gjøre sjekk under i ACL, og å returnere behov for rerouting. Constants burde settes i $route, ikke $routing.
		if($acl->visitorIsBlocked($visitor)) {
			$route = $routing->redirect($routing::ERROR_403_URL);
		}
		
		// Perform RBAC
		if(!$acl->visitorHasAccess($visitor, $route)) {
			if($route->isRedirect) {
				$route = $routing->redirect($routing::ERROR_500_URL);
			} else {
				if($visitor->isLoggedIn())
				{
					$route = $routing->redirect($routing::ERROR_403_URL);
				} 
				else
				{
					$route = $routing->redirect($routing::LOGIN_URL);
				}
			}
		}
		*/
		
		// Dispatch to whatever route we ended up with
		$this->dispatcher->dispatch($route);
	}
}