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
	
	protected function load($sCommand)
	{
		$sCommand = ucfirst(strtolower($sCommand));
		
		$mutator = 'execute' . $sCommand;

		if (!method_exists($this, $mutator) || !is_callable(array($this, $mutator))) {
			throw new \Exception($sCommand . ' could not be called on View');
		}
		
		return $this->$mutator();
	}
	
	public function execute($sCommand)
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
		
		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);
			
		/** Get list of all visitors */
		$this->presentationObjectFactory
			->build('activeVisitors', true)
			->assignData($this->serviceFactory->build('recognition', true)->getActiveVisitors());
						
		return $this->load($sCommand);
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