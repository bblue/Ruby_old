<?php
namespace View;

use App\Factories\Service as ServiceFactory,
	App\Factories\PresentationObject as PresentationObjectFactory;
	
use App\Boot\Request;

abstract class AbstractView
{
	protected $serviceFactory;
	protected $presentationObjectFactory;
	protected $request;
	
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
		
		$this->template->set_custom_template(ROOT_PATH . 'view/templates/' . SITE_TEMPLATE, 'templateName');
	}
	
	public function setCommand($sCommand)
	{
		$this->sCommand = $sCommand;
		return true;
	}
	
	protected function load($sCommand = '')
	{
		if(empty($sCommand))
		{
			throw new \Exception('Command is empty. Unable to load'); // @todo: Consider if indexAction should be loaded by default.
		}
		
		$sCommand = ucfirst(strtolower($sCommand));
		
		$mutator = 'execute' . $sCommand;

		if (!method_exists($this, $mutator) || !is_callable(array($this, $mutator))) {
			throw new \Exception($sCommand . ' could not be called on View');
		}
		
		return $this->$mutator();
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

		// $sCommand should take priority over $this->sCommand
		$this->sCommand = !empty($sCommand) ? $sCommand : $this->sCommand;

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
			$sTemplateFile = 'extras-403';
					
			$this->display('custom/header.htm');
			$this->display('custom/sidebar.htm');
			$this->display('custom/rightbar.htm');
			$this->display('custom/' . $sTemplateFile . '.htm');
			$this->display('custom/footer.htm');	
		}
		else
		{
			$this->display('custom/full-page-error.htm');
		}
	}
		
	protected function display($sTemplateFile)
	{
		switch($this->request->getReturnDataType())
		{
			default: case 'template':
				return $this->displayTemplate($sTemplateFile);
				break;
			case 'json':
				return $this->displayJSON();
				break;
		}
	}
	
	protected function displayJSON()
	{
		foreach($this->presentationObjectFactory->getCache() as $presentationObject)
		{
			echo json_encode($presentationObject->getAllVars());
			return true;
		}		
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