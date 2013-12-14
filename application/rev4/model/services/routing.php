<?php
namespace Model\Services;

use Model\Domain\Visitor\Visitor;
use App\ServiceAbstract;

final class Routing extends ServiceAbstract
{
	private $sOriginalUrl;
	private $route;
	private $visitor;
	private $acl;

	const MAINTENANCE_URL 	= 'controller/action';
	const ERROR_403_URL		= 'error/403';
	const ERROR_404_URL		= 'error/404';
	const ERROR_500_URL		= 'error/500';
	const LOGIN_URL			= 'controller/login';
	const DEFAULT_URL		= 'recipes';

	public function route($sUrl, Visitor $visitor, ACL $acl)
	{
		$this->sOriginalUrl = $sUrl;
		$this->visitor = $visitor;
		$this->acl = $acl;
		
		$this->route = $this->buildRoute(!empty($this->sOriginalUrl) ? $this->sOriginalUrl : self::DEFAULT_URL);

		if($this->acl->visitorIsBlocked($this->visitor)) {
			return $this->redirect(self::ERROR_403_URL);
		}

		if($sRedirectUrl = $this->executeRedirectRules()) {
			$this->redirect($sRedirectUrl);
		}

		if(!$this->acl->visitorHasAccess($this->visitor, $this->route)) {
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
		$this->setModelState('error', $this->createLogEntry('Redirect to ' . $sRedirectUrl . ' detected', $this->visitor));
		
		return $this->route = $this->buildRoute($sRedirectUrl);
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
		if($this->route->id)
		{
			if($this->route->isEnabled())
			{
				return false;  
			}
		}
		return true;
	}
	
	private function redirect_to_403()
	{
		//return $this->route->isEnabled();
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
			if($this->route->canBypassForcedLogin())
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
		return false;
	}
}