<?php
namespace View\Views;
use View\AbstractView, View\Template;

final class Recipes extends AbstractView
{
	public function indexAction()
	{
		/** Prepare for possible error output */
		$this->presentationObjectFactory
			->build('serverresponse', true)
			->setPresentationName('error')
			->assignData($this->serviceFactory->build('model')->getModelResponse('error'));

		/** Get current visitor information */
		$this->presentationObjectFactory
			->build('visitor', true)
			->assignData($this->serviceFactory->build('recognition')->getCurrentVisitor());

		/** Get list of all visitors */
		$this->presentationObjectFactory
			->build('activeVisitors', true)
			->assignData($this->serviceFactory->build('recognition')->getActiveVisitors());
		
		return $this->display('sticky_footer.html');
	}
	
	public function login()
	{
		/** Prepare for possible error output */
		$this->presentationObjectFactory
			->build('serverresponse', true)
			->setPresentationName('error')
			->assignData($this->serviceFactory->build('model')->getModelResponse('error'));
			
		/** Prepare server response */
		$this->presentationObjectFactory
			->build('serverresponse', true)
			->setPresentationName('success')
			->assignData($this->serviceFactory->build('model')->getModelResponse('success'));
			
		/** Get current visitor information */
		$this->presentationObjectFactory
			->build('visitor', true)
			->assignData($this->serviceFactory->build('recognition')->getCurrentVisitor());	
				
		return $this->serviceFactory->build('recognition')->getCurrentVisitor()->isLoggedIn() ? 
					$this->indexAction() : 
					$this->display('login.html');
	}
	
	public function logout()
	{
		/** Prepare for possible error output */
		$this->presentationObjectFactory
			->build('serverresponse', true)
			->setPresentationName('error')
			->assignData($this->serviceFactory->build('model')->getModelResponse('error'));
			
		/** Prepare server response */
		$this->presentationObjectFactory
			->build('serverresponse', true)
			->setPresentationName('success')
			->assignData($this->serviceFactory->build('model')->getModelResponse('success'));
			
		return $this->indexAction();
	}
} 