<?php
namespace Model\Services;

use Model\Domain\Route\Route;

use App\Boot\Request;

use Model\Domain\Visitor\Visitor;
use App\ServiceAbstract;

final class Routing extends ServiceAbstract
{
	private $sOriginalUrl;
	public $route;
	private $visitor;
	private $request;

	const MAINTENANCE_URL 	= 'error/maintenance';
	const ERROR_403_URL		= 'error/403';
	const ERROR_404_URL		= 'error/404';
	const ERROR_500_URL		= 'error/500';
	const LOGIN_URL			= 'users/login';
	const DEFAULT_URL		= 'index';

	const FORCED_LOGIN		= FORCED_LOGIN;

	public function route(Request $request)
	{
		if(!is_object($this->visitor)){
			throw new \Exception('Routing mechanism requires a valid visitor object');
		}

		$this->sOriginalUrl = $request->getUrl();

		$this->route = $this->buildRoute(!empty($this->sOriginalUrl) ? $this->sOriginalUrl : self::DEFAULT_URL);

		if($sRedirectUrl = $this->executeRedirectRules()) {
			$this->redirect($sRedirectUrl);
		}

		return $this->route;
	}

	public function setVisitor(Visitor $visitor)
	{
		$this->visitor = $visitor;
	}

	private function executeRedirectRules()
	{
		// Test if the website is up
		if($this->redirect_to_maintenance_page()) {
			return self::MAINTENANCE_URL;
		}

		// Test if forced login is in effect
		if($this->redirect_to_login_page()) {
			return self::LOGIN_URL;
		}

		// Test if the route exists
		if($this->redirect_to_404())  {
			return self::ERROR_404_URL;
		}

		// Test if route is enabled and user has access
		if($this->redirect_to_403()) {
			return self::ERROR_403_URL;
		}

		// Test if there are user specific reroute rules in effect
		if($sRedirectUrl = $this->redirect_to_user_specific_rule()) {
			return $sRedirectUrl;
		}

		// Check for site specific permanent redirection
		if($sRedirectUrl = $this->redirect_to_301()) {
			//@todo: denne er nyttig hvis jeg endrer noe i url syntaxen. Jeg lagrer den gamle i databaesn, og returnerer denne koden. View må få vite om disse ulike response kodene på en eller annen måte.
			return $sRedirectUrl;
		}
	}

	public function redirect($sRedirectUrl)
	{
		// Completely clear out the old route
		unset($this->route);

		$sMessage = 'Redirect to ' . $sRedirectUrl . ' detected';
		$this->log->createLogEntry($sMessage, $this->visitor, 'info', SHOW_ROUTE_BUILD_MESSAGES);

		$this->route = $this->buildRoute($sRedirectUrl);

		$this->route->isRedirect = true;

		return $this->route;
	}

	private function buildRoute($url, $iLevel = 1)
	{
		// Create the initial route entity
		$route = $this->entityFactory->build('route');

		// Pre-fill the route with the url
		$route->url = $url;

		if(!$this->buildRouteFromTable($route, $iLevel)) {
			if(ALLOW_PATH_ROUTE_BUILD === true) {
				$this->buildRouteFromPath($route, $iLevel);
			}
		}

		// Check for entity errors
		if($route->hasError()) {
			throw new \Exception('Route should not throw an error. Something is very wrong.');
		}

		// Return route
		return $route;
	}

	private function loadRouteTier($route, $iLevel)
	{
		// We build backwards, so recalc the level
		$iTier = $route->iUrlLevels - ($iLevel - 1);

		// Confirm that the tier level is within range
		if($iTier > $route::MAX_LEVEL || $iTier > $route->iUrlLevels || $iTier < 1) {
			return false;  // No route was found
		}

		// Get the resource and command from current tier
		$route->sResourceName = $route->extractControllerFromUrl($iTier);
		$route->sCommand = $route->extractCommandFromUrl($iTier);

		return $route;
	}

	private function buildRouteFromTable(Route $route, $iLevel)
	{
		// Get the specific route level data
		if(!$this->loadRouteTier($route, $iLevel)) {
			return false; // not within range, i.e. no route was found
		}

		// Attempt to load the route
		$this->dataMapperFactory
			->build('route')
			->fetch($route);

		// Test for valid route
		if(empty($route->id)) {
			// Log the route build attempt
			$sMessage = '['.$iLevel.'] Route build fail -routeTable ('.$route->sResourceName.'->'.$route->getCommand().')';
			$this->log->createLogEntry($sMessage, $this->visitor, 'info', SHOW_ROUTE_BUILD_MESSAGES);

			// Try to load on the next level
			return $this->buildRouteFromTable($route, $iLevel + 1);
		} else {
			// Log the route build attempt
			$sMessage = '['.$iLevel.'] Route build success -routeTable ('.$route->sResourceName.'->'.$route->getCommand().')';
			$this->log->createLogEntry($sMessage, $this->visitor, 'success', SHOW_ROUTE_BUILD_MESSAGES);

			// Route was found, return it
			return $route;
		}
	}

	private function buildRouteFromPath(Route $route, $iLevel)
	{
		// Get the specific route level data
		if(!$this->loadRouteTier($route, $iLevel)) {
			return false;
		}

		// Attempt to load the route
		$this->dataMapperFactory
			->build('routecontroller')
			->fetch($route);

		if(empty($route->id)) {
			// Log the route build attempt
			$sMessage = '['.$iLevel.'] Route build fail -path ('.$route->sResourceName.'->'.$route->getCommand().')';
			$this->log->createLogEntry($sMessage, $this->visitor, 'info', SHOW_ROUTE_BUILD_MESSAGES);

			// Route was found, return it
			return $this->buildRouteFromPath($route, $iLevel + 1);
		} else {
			// Log the route build attempt
			$sMessage = '['.$iLevel.'] Route build success -path ('.$route->sResourceName.'->'.$route->getCommand().')';
			$this->log->createLogEntry($sMessage, $this->visitor, 'success', SHOW_ROUTE_BUILD_MESSAGES);

			// Route was found, return it
			return $route;
		}
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
		// Check if we have a route id
		if(empty($this->route->id)) {
			return true;
		}

		// Check if the route is enabled
		if($this->route->isEnabled()) {
			return false;
		}

		// Route does not exists, check if we should still attempt to load it
		if(defined('DEV_AREA_CONFIRMED') && (DEV_AREA_CONFIRMED === true) && (!defined('BYPASS_IS_ENABLED_CHECK') || BYPASS_IS_ENABLED_CHECK === false)) {
			return true;
		}

		// We are in dev mode and can attempt to load the route. Check if we have permission
		if($this->visitor->user->isAdmin()) 	{
			trigger_error('Now bypassing route verification. Fatal errors could be triggered', E_USER_NOTICE);
			return false;
		}

		return true;
	}

	private function redirect_to_403()
	{
		//@todo: This check should also verify that the user is not blocked

		require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'lib'. DIRECTORY_SEPARATOR . 'PhpRbac'. DIRECTORY_SEPARATOR . 'autoload.php'; //@todo: Create a service from this
		$rbac = new \PhpRbac\Rbac();

		$sPath = '/'.strtoupper($this->route->sResourceName.'/'.$this->route->getCommand());

		if(defined('DEV_AREA_CONFIRMED') && (DEV_AREA_CONFIRMED === true) && (!defined('CREATE_PERMISSIONS') || CREATE_PERMISSIONS === true)) {
			if($rbac->Permissions->returnId($sPath) === null) {
				if($rbac->Permissions->AddPath($sPath, array())) {
					$sMessage = 'RBAC path added';
					$this->log->createLogEntry($sMessage, $this->visitor, 'success');
				}

			}
		}

		//$guestRoleID = 21;
		//$rbac->Permissions->assign($guestRoleID, 63);
		//$rbac->Users->assign($guestRoleID, 0);
		//$rbac->Roles->addPath('/developer/global_admin/recipe_admin/recipe_moderator/recipe_writer/recipe_member/recipe_guest');

		if(!$rbac->check($sPath, $this->visitor->user_id)) {
			return true;
		}
	}

	private function redirect_to_maintenance_page()
	{
		if($this->route->url == self::MAINTENANCE_URL) {
			return false;
		}

		if($this->website_is_online()) {
			return false;
		}

		if($this->visitor->is_localhost()) {
			return false;
		}

		if($this->visitor->is_admin()) {
			return false;
		}

		// Redirect to maintenance page
		return true;
	}

	private function redirect_to_login_page()
	{
		if($this->route->url == self::LOGIN_URL) {
			return false;
		}

		if($this->visitor->isLoggedIn()) {
			return false;
		}

		if($this->is_forced_login()) {
			return !$this->route->canBypassForcedLogin();
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