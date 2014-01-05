<?php
namespace View\Views;
use View\AbstractView, View\Template;

final class Error extends AbstractView
{
	public function executeSet404error()
	{
		http_response_code(404);

		$this->presentationObjectFactory
			->build('errormessage', true)
			->setTemplatePrefix('http_error')
			->assignData(404, 'Page Not Found', 'The page you requested could not be found');
		
		if($this->serviceFactory->build('recognition', true)->getCurrentVisitor()->isLoggedIn() || FORCED_LOGIN === false)
		{
			$sTemplateFile = 'extras-404';
					
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
		
		return true;
	}
	
	public function executeSet500error()
	{		
		http_response_code(500);
		
		$this->presentationObjectFactory
			->build('errormessage', true)
			->setTemplatePrefix('http_error')
			->assignData(500, 'Internal Server Error', 'An internal server error occured');
		
		if($this->serviceFactory->build('recognition', true)->getCurrentVisitor()->isLoggedIn() || FORCED_LOGIN === false)
		{
			$sTemplateFile = 'extras-500';
					
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
		
		return true;
	}	
	
} 