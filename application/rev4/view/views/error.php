<?php
namespace View\Views;
use View\AbstractView, View\Template;

final class Error extends AbstractView
{
	public function executeIndexAction()
	{
		$this->display('overall_header.html');
		$this->display('pages/error.html');
		$this->display('overall_footer.html');
		
		return true;
	}
	
	public function executeSet403error()
	{
		$this->presentationObjectFactory
			->build('errormessage', true)
			->setPresentationName('http_error')
			->assignData(403, 'Forbidden', 'You do not have access to this area');
			
		http_response_code(403);
			
		return $this->load('indexAction');
	}
	
	public function executeSet404error()
	{
		$this->presentationObjectFactory
			->build('errormessage', true)
			->setPresentationName('http_error')
			->assignData(403, 'Page Not Found', 'The page you requested could not be found');
			
		http_response_code(404);
		
		return $this->load('indexAction');
	}
	
	public function executeSet500error()
	{
		$this->presentationObjectFactory
			->build('errormessage', true)
			->setPresentationName('http_error')
			->assignData(500, 'Internal Server Error', 'An internal server error occured');
		
		http_response_code(500);
			
		return $this->load('indexAction');
	}	
	
} 