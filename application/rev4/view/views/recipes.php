<?php
namespace View\Views;
use View\AbstractView,
	View\Template;

final class Recipes extends AbstractView
{
	public function executeIndexAction()
	{
		/** Get list of all visitors */
		$this->presentationObjectFactory
			->build('activeVisitors', true)
			->assignData($this->serviceFactory->build('recognition')->getActiveVisitors());

		/** Render the pages */
		$this->display('overall_header.html');
		$this->display('overall_navigation.html');
		$this->display('pages/recipes.html');
		$this->display('overall_footer.html');	
	}
} 