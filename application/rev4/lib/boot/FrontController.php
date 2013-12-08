<?php

$frontController = new FrontController($serviceFactory);
$frontController->run($request);

#######

class FrontController
{
	private $serviceFactory;
	
	private $routing;
	
	private $dispatcher;
	
	public function __construct( ServiceFactory $serviceFactory)
	{
		$this->serviceFactory = $serviceFactory;
	}
	
	public function run(RequestInterface $request)
	{
		// Get hold of the visitor for the request. This is the first time we are getting the visitor.
		$visitor = $serviceFactory->build('recognition')->getCurrentVisitor();

		$dispatcher = new Dispatcher($this->serviceFactory);

		$routing = $this->serviceFactory->build('routing');
		
		// Run the requested route via the routing mechanism, and check it towards the ACL
		$sanitizedRoute = $routing->route($request->getRoute(), $visitor);

		// Dispatch to whatever route we ended up with
		$dispatcher->dispatch($sanitizedRoute, $request);

		// Return data to the user based on model state
		$view->display();
	}
}

interface DispatcherInterface 
{
	public function dispatch(RouteInterface $sanitizedRoute, ServiceFactory $serviceFactory, RequestInterface $request);
}

final class Dispatcher
{
	private $serviceFactory;

	public function setServiceFactory(ServiceFactory $serviceFactory)
	{
		$this->serviceFactory = $serviceFactory;
	}

	private function createController($sControllerName)
	{
		return $controller;
	}
	
	private function createView($sViewName)
	{
		return $view;
	}
	
	public function dispatch(RouteInterface $route, RequestInterface $request)
	{
		$controller = $this->createController($route->getResourceName());

		$view = $this->createView($route->getResourceName());

		$sCommand = $route->getCommand();

		// This line does all the magic
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
			$view->setError($e->getMessage());
		}

		return true;
	}
}

final class Routing extends AbstractService
{
	private $sOriginalUrl;
	private $route;
	private $visitor;

	const MAINTENANCE_URL 	= 'controller/action';
	const ERROR_404_URL		= 'controller/404';
	const ERROR_403_URL		= 'controller/403';
	const ERROR_500_URL		= 'controller/500';

	public function route($sUrl, Visitor $visitor)
	{
		$this->sOriginalUrl = $sUrl;
		$this->visitor = $visitor;

		$this->route = $this->buildRoute($this->sOriginalUrl);

		if($this->visitorIsBlocked()) {
			return $this->redirect(self::ERROR_403_URL);
		}

		if($sRedirectUrl = $this->executeRedirectRules()) {
			$this->redirect($sRedirectUrl);
		}

		$acl = new ACL($this->route, $this->visitor);
		if(!$acl->visitorHasAccess()) {
			if($sRedirectUrl) {
				$this->redirect(self::ERROR_500_URL);
			} else {
				$this->redirect(self::ERROR_403_URL);
			}
		}

		return $this->route;
	}

	private function executeRedirectRules()
	{
		// Test if the website is up
		if($this->redirect_to_maintenance_page())
		{
			return self::MAINTENANCE_URL;
		}
		
		// Test if the route exists
		if($this->redirect_to_404())
		{
			return self::ERROR_404_URL;
		}
		
		// Test if forced login is in effect
		if($this->redirect_to_login_page())
		{
			return self::LOGIN_URL;
		}

		// Test if route is enabled
		if($this->redirect_to_403())
		{
			return self::ERROR_403_URL;
		}

		// Test if there are user specific reroute rules in effect
		if($sRedirectUrl = $this->redirect_to_user_specific_rule())
		{
			return $sRedirectUrl;
		}
		
		// Check for site specific permanent redirection
		if($sRedirectUrl = $this->redirect_to_301())
		{
			//@todo: denne er nyttig hvis jeg endrer noe i url syntaxen. Jeg lagrer den gamle i databaesn, og returnerer denne koden. View må få vite om disse ulike response kodene på en eller annen måte.
			return $sRedirectUrl;
		}
	}

	private function redirect($sRedirectUrl)
	{
		$this->createLogEntry('Redirect to ' . $sRedirectUrl . ' detected';
		return $this->route = $this->buildRoute($sRedirectUrl);
	}
	
	private function buildRoute($sUrl)
	{
		$route = $this->entityFactory->build('route');
		
		$route->url = $sUrl;
		
		$this->dataMapperFactory
			->build('route')
			->fetch($route);
			
		return $route;
	}
	

	public function visitorIsBlocked()
	{
		// Check that the IP is not blocked
		
		// Check that the userID is not blocked
		
	}

	private function redirect_to_user_specific_rule()
	{
		if($this->visitor->user->redirect_url)
		{
			return $this->redirect($this->visitor->user->redirect_url);
		}
	}

	private function redirect_to_404()
	{
		// Check if the route exists in the database
		// Check if the route can be split into controller and action, then test this against DB
	}

	private function redirect_to_maintenance_page()
	{
		if($this->route->url == self::MAINTENANCE_URL)
		{
			return false;
		}
		
		if($this->website_is_online())
		{
			return false;
		}
		
		if($visitor->is_localhost())
		{
			return false;
		}
		
		if($visitor->is_admin())
		{
			return false;
		}

		// Redirect to maintenance page
		return true;
	}

	private function redirect_to_login_page()
	{
		if($route->url == self::LOGIN_URL )
		{
			return false;
		}
		
		if($visitor->isLoggedIn())
		{
			return false;
		}

		if($this->is_forced_login())
		{
			if($route->canBypassForcedLogin())
			{
				return false;
			}

			// Redirect to login page
			return true;
		}
	}
	
	private function check_for_invalid_ip()
	{
		$blocked_ips = $this->dataMapperFactory->build('blocked_ips')->findAll();
		if(in_array($this->visitor->ip, $blocked_ips->ip))
		{
			throw new RouteException($visitor->ip ' does not have access to this route');
		}
	}
	
	private function redirect_to_403()
	{
		if(!$this->route->is_enabled())
		{
			return true;
		}
	}
}


final class ACL implements InterfaceACL
{
	private $visitor;
	private $route;
	private $bAccess;
	
	public function __construct(RouteInterface $route, VisitorInterface $visitor)
	{
		$this->visitor = $visitor;
		$this->route = $route;
	}

	public function visitorHasAccess()
	{
		if(!isset($this->bAccess)) {
			$this->bAccess = ($this->testUsergroupAccess()) ? : $this->testUserAccess();
		}
		return $bAccess;
	}

	private function testUsergroupAccess()
	{
		foreach($this->visitor->user->usergroups as $usergroup) {
			if($this->route->usergroupHasAccess($usergroup->id)) {
				return true;
			}
		}
	}
	
	private function testUserAccess()
	{
		
	}
	
}

interface InterfaceACL
{
	public function visitorHasAccess();
	public function visitorIsBlocked();
}

########### old ############
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