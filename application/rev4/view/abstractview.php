<?php
namespace View;
use App\Factories\Service as ServiceFactory;;
use App\Factories\PresentationObject as PresentationObjectFactory;
use App\Boot\Request;

abstract class AbstractView
{
	public $DEFAULT_ACTION = 'indexAction';
	public $SITE_TEMPLATE;
	
	protected $serviceFactory;
	protected $presentationObjectFactory;
	protected $request;
	
	protected $template;
	
	public function __construct(ServiceFactory $serviceFactory, Request $request)
	{
		$this->serviceFactory = $serviceFactory;
		$this->request = $request;
		$this->presentationObjectFactory = new PresentationObjectFactory();
		
		if(!defined('WEBSITE')) { throw new \Exception('WEBSITE constant has not been set'); }
		if(!defined('SITE_TEMPLATE')) { throw new \Exception('SITE_TEMPLATE constant has not been set'); } //@todo: hente template fra databasen
	}
	
	protected function display($sTemplateFile)
	{
		switch($this->request->getReturnDataType())
		{
			default: case 'template':
				$this->displayTemplate($sTemplateFile);
				break;
			case 'json':
				$this->displayJSON();
				break;
		}
	}
	
	protected function displayJSON()
	{
		foreach($this->presentationObjectFactory->getCache() as $presentationObject)
		{
			echo json_encode($presentationObject->getAllVars());
		}		
	}
	
	protected function displayTemplate($sTemplateFile)
	{
		if(!is_object($this->template))
		{
			$this->template = new Template();
			$this->template->set_custom_template(ROOT_PATH . 'view/templates', 'templateName');	
		}
			
		foreach($this->presentationObjectFactory->getCache() as $presentationObject)
		{
			$this->template->assign_vars($presentationObject->getVars());
			$aBlockVars = $presentationObject->getBlockVars();
			foreach($aBlockVars as $blockname => $block)
			{
				foreach($block as $key => $vararray)
				{
					$this->template->assign_block_vars($blockname, $vararray);
				}
			}
		}

		$this->template->set_filenames(array($sTemplateFile => $sTemplateFile));

		$this->template->display($sTemplateFile);
	}
	
	public function indexAction()
	{
		throw new \Exception('Unable to identify index action called by ' . get_called_class());
	}
}