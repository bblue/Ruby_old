<?php
namespace Modules;
use View\AbstractView, View\Template;

final class ErrorView extends AbstractView
{
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
	
	public function executeSet500error()
	{		
		http_response_code(500);
		
		$this->presentationObjectFactory
			->build('errormessage', true)
			->setTemplatePrefix('http_error')
			->assignData(500, 'Internal Server Error', 'An internal server error occured');

		$sTemplateFile = 'error/extras-500';
		
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
	
} 