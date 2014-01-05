<?php
namespace Model\Services;

use Model\Domain\Visitor\Visitor;
use App\ServiceAbstract;

final class Routing extends ServiceAbstract
{
	private $sOriginalUrl;
	private $route;
	private $visitor;

	const MAINTENANCE_URL 	= 'error/maintenance';
	const ERROR_403_URL		= 'error/403';
	const ERROR_404_URL		= 'error/404';
	const ERROR_500_URL		= 'error/500';
	const LOGIN_URL			= 'users/login';
	const DEFAULT_URL		= 'index';
	
	const FORCED_LOGIN		= FORCED_LOGIN;

	public function route($sUrl, Visitor $visitor)
	{
		$this->sOriginalUrl = $sUrl;
		$this->visitor = $visitor;
		
		$this->route = $this->buildRoute(!empty($this->sOriginalUrl) ? $this->sOriginalUrl : self::DEFAULT_URL);

		if($sRedirectUrl = $this->executeRedirectRules()) {
			$this->redirect($sRedirectUrl);
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
		
	    // Test if forced login is in effect
		if($this->redirect_to_login_page())
		{
			return self::LOGIN_URL;
		}
		
		// Test if the route exists
		if($this->redirect_to_404())
        {
        	return self::ERROR_404_URL;
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

	public function redirect($sRedirectUrl)
	{
		// Completely clear out the old route
		unset($this->route);
		
		$sMessage = 'Redirect to ' . $sRedirectUrl . ' detected';
		$this->log->createLogEntry($sMessage, $this->visitor, 'info', true);
		
		$this->route = $this->buildRoute($sRedirectUrl);
		
		$this->route->isRedirect = true;
		
		return $this->route;
	}
	
	private function buildRoute($url)
	{
		// Create the initial route entity
		$route = $this->entityFactory->build('route');
		
		// Pre-fill the route with the url
		$route->url = $url;
		
		// Load the route specific items from the database.
		$this->dataMapperFactory
			->build('route')
			->fetch($route);

		// Confirm route match in database
		if(!$route->id)
		{
			// No route match. Attempt to load the route from the url split
			$route->sResourceName = $route->extractControllerFromUrl($route->url);
			$route->sCommand = $route->extractCommandFromUrl($route->url);
			$this->dataMapperFactory
				->build('routecontroller')
				->fetch($route);
		}

		// Check for entity errors
		if($route->hasError())
		{
			throw new \Exception('Route should not throw an error. Something is very wrong.');
		}

		return $route;
	}

	private function redirect_to_user_specific_rule()
	{
		/*
		if($this->visitor->user->redirect_url)
		{
			return $this->redirect($this->visitor->user->redirect_url);
		}
		*/
	}
	
	private function redirect_to_301()
	{
		
	}
	
	private function redirect_to_404()
	{
		// Check if the route is enabled. This will also verify if the route exists
		if($this->route->isEnabled())
		{
			return false; 
		}
		
		// Route does not exists, check if we should still attempt to load it
		if(!defined('BYPASS_IS_ENABLED_CHECK') || BYPASS_IS_ENABLED_CHECK === false)
		{
			return true;
		}
		
		// We are in dev mode and can attempt to load the route. Check if we have permission
		if($this->visitor->user->isAdmin())
		{
			trigger_error('Now bypassing route verification. Fatal errors could be triggered', E_USER_NOTICE);
			return false;
		}

		return true;
	}
	
	private function redirect_to_403()
	{
		// This check should verify that the user is not blocked
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
		if($this->route->url == self::LOGIN_URL )
		{
			return false;
		}
		
		if($this->visitor->isLoggedIn())
		{
			return false;
		}

		if($this->is_forced_login())
		{
			if($this->route->bCanBypassForcedLogin)
			{
				return false;
			}

			// Redirect to login page
			return true;
		}
	}
	
	private function website_is_online()
	{
		return true;
	}

	private function is_forced_login()
	{
		return self::FORCED_LOGIN;
	}
}