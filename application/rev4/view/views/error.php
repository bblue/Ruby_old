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
	}
} 