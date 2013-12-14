<?php
namespace View\Views;
use View\AbstractView, View\Template;

final class Error extends AbstractView
{
	public function indexAction()
	{
		/** Get current visitor information */
		$this->presentationObjectFactory
			->build('visitor', true)
			->assignData($this->serviceFactory->build('recognition')->getCurrentVisitor());
			
		/** Prepare for possible error output */
		$this->presentationObjectFactory
			->build('serverresponse', true)
			->setPresentationName('error')
			->assignData($this->serviceFactory->build('model')->getModelResponse('error'));
		
		$this->display('overall_header.html');
		$this->display('overall_navigation.html');
		$this->display('error.html');
		$this->display('overall_footer.html');
		
		return true;
	}
	
	public function set403error()
	{
		$this->presentationObjectFactory
			->build('errormessage', true)
			->setPresentationName('http_error')
			->assignData(403, 'Forbidden', 'You do not have access to this area');
			
		http_response_code(403);
			
		return $this->indexAction();
	}
	
	public function set404error()
	{
		$this->presentationObjectFactory
			->build('errormessage', true)
			->setPresentationName('http_error')
			->assignData(403, 'Page Not Found', 'The page you requested could not be found');
			
		http_response_code(404);
		
		return $this->indexAction();
	}
	
	public function set500error()
	{
		$this->presentationObjectFactory
			->build('errormessage', true)
			->setPresentationName('http_error')
			->assignData(500, 'Internal Server Error', 'An internal server error occured');
		
		http_response_code(500);
			
		return $this->indexAction();
	}	
	
} 