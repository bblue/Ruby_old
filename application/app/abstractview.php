<?php
namespace App;

use App\Factories\Service as ServiceFactory,
	App\Factories\PresentationObject as PresentationObjectFactory;

use App\Boot\Request;

abstract class AbstractView
{
	protected $serviceFactory;
	protected $presentationObjectFactory;
	protected $request;

	private $mControllerResponse;

	private $sCommand = '';

	protected $template;

	public function __construct(ServiceFactory $serviceFactory, Request $request)
	{
		$this->serviceFactory 				= $serviceFactory;
		$this->request 						= $request;
		$this->template  					= new Template();
		$this->presentationObjectFactory 	= new PresentationObjectFactory($this->template);

		if(!defined('WEBSITE')) { throw new \Exception('WEBSITE constant has not been set'); }
		if(!defined('SITE_TEMPLATE')) { throw new \Exception('SITE_TEMPLATE constant has not been set'); } //@todo: hente template fra databasen

		$this->template->set_custom_template(ROOT_PATH . DIRECTORY_SEPARATOR . 'view'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR . SITE_TEMPLATE, 'templateName');
	}

	public function setControllerResponse($mControllerResponse)
	{
		$this->mControllerResponse = $mControllerResponse;
		return $this;
	}

	public function setCommand($sCommand)
	{
		$this->sCommand = $sCommand;
		return true;
	}

	protected function load($sCommand = '')
	{
		if(empty($sCommand)) {
			throw new \Exception('Command is empty. Unable to load'); // @todo: Consider if indexAction should be loaded by default.
		}

		$sCommand = ucfirst(strtolower($sCommand));

		$mutator = 'execute' . $sCommand;

		if (!method_exists($this, $mutator) || !is_callable(array($this, $mutator))) {
			throw new \Exception($sCommand . ' could not be called on View');
		}

		return $this->$mutator($this->mControllerResponse);
	}

	protected function redirect($sUrl, $bPermanent = false)
	{
		if(headers_sent($filename, $linenum)) {
			throw new \Exception('Cannot modify header information - headers already sent in '.$filename.' on line '.$linenum);
		}
		header('Location:'.$sUrl, true, $bPermanent ? 301 : 302);
		exit();
	}

	public function execute($sCommand = '')
	{
		/** Build the log reports */
		$this->presentationObjectFactory
			->build('log', true)
			->assignData($this->serviceFactory->build('logging', true)->getCurrentLogs());

		/** Get current visitor information */
		$this->presentationObjectFactory
			->build('visitor', true)
			->setTemplatePrefix('visitor')
			->assignData($this->serviceFactory->build('recognition', true)->getCurrentVisitor());

		/** Load the user info for top menu */
		$this->presentationObjectFactory
			->build('nav_userinfo', true)
			->assignData($this->serviceFactory->build('recognition', true)->getCurrentVisitor());

		/** Load the user inbox for top menu  */
		$this->presentationObjectFactory
			->build('nav_userinbox', true)
			->assignData($this->serviceFactory->build('recognition', true)->getCurrentVisitor());

		/** Load the user notifications for top menu  */
		$this->presentationObjectFactory
			->build('nav_usernotifications', true)
			->assignData($this->serviceFactory->build('recognition', true)->getCurrentVisitor());

		/** Get list of all visitors */
		$this->presentationObjectFactory
			->build('activeVisitors', true)
			->assignData($this->serviceFactory->build('recognition', true)->getActiveVisitors());

		// Assign global template vars
		$this->template->assign_var('GLOBAL_SERVER_NAME', $_SERVER['SERVER_NAME']);
		$this->template->assign_var('GLOBAL_HOST_NAME', gethostname());
		$this->template->assign_var('GLOBAL_QUERY_STRING', $_SERVER['QUERY_STRING']);
		$this->template->assign_var('GLOBAL_URL_PARAM_0', $this->request->_url(0));
		$this->template->assign_var('GLOBAL_URL_PARAM_1', $this->request->_url(1));

		// Check redirection var
		$routing = $this->serviceFactory->build('routing', true);
		$route = $routing->route;
		$this->template->assign_var('GLOBAL_TARGET_URL', ($route->isRedirect) ? urlencode(base64_encode('/'.$routing->getOriginalUrl() .'?'. $this->request->_server('QUERY_STRING'))) : false);

		// $sCommand should take priority over $this->sCommand
		$this->sCommand = !empty($sCommand) ? $sCommand : $this->sCommand;

		// Set meta tag
		if(!headers_sent()) {
			header('Content-Type: text/html; charset=utf-8');
		}

		return $this->load($this->sCommand);
	}

	public function executeSet403error()
	{
		$this->presentationObjectFactory
			->build('errormessage', true)
			->setTemplatePrefix('http_error')
			->assignData(403, 'Forbidden', 'You do not have access to this area');

		http_response_code(403);

		if($this->serviceFactory->build('recognition', true)->getCurrentVisitor()->isLoggedIn() || FORCED_LOGIN === false)
		{
			$sTemplateFile = 'error'.DIRECTORY_SEPARATOR.'extras-403';

			$this->display('custom'.DIRECTORY_SEPARATOR.'header.htm');
			$this->display('custom'.DIRECTORY_SEPARATOR.'sidebar.htm');
			$this->display('custom'.DIRECTORY_SEPARATOR.'rightbar.htm');
			$this->display('custom'.DIRECTORY_SEPARATOR. $sTemplateFile . '.htm');
			$this->display('custom'.DIRECTORY_SEPARATOR.'footer.htm');
			return true;
		} else {
			$this->display('custom'.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.'full-page-error.htm');
			return true;
		}
	}

	public function executeSet404error()
	{
		http_response_code(404);

		$this->presentationObjectFactory
			->build('errormessage', true)
			->setTemplatePrefix('http_error')
			->assignData(404, 'Page Not Found', 'The page you requested could not be found');

		$sTemplateFile = 'error/extras-404';

		/** Load required scripts */
		$this->presentationObjectFactory
		->build('scripttags', true)
		->assignData($sTemplateFile);

		if($this->serviceFactory->build('recognition', true)->getCurrentVisitor()->isLoggedIn() || FORCED_LOGIN === false) {
			$this->display('custom/header.htm');
			$this->display('custom/sidebar.htm');
			$this->display('custom/rightbar.htm');
			$this->display('custom/' . $sTemplateFile . '.htm');
			$this->display('custom/footer.htm');
		} else {
			$this->display('custom/error/full-page-error.htm');
		}

		return true;
	}

	protected function display($sTemplateFile)
	{
		return ($this->request->isAjaxRequest()) ? : $this->displayTemplate($sTemplateFile);
	}

	protected function displayTemplate($sTemplateFile)
	{
		$this->template->set_filenames(array($sTemplateFile => $sTemplateFile));

		return $this->template->display($sTemplateFile);
	}

	public function indexAction()
	{
		throw new \Exception('Unable to identify index action called by ' . get_called_class());
	}
}