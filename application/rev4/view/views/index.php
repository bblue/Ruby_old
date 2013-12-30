<?php
namespace View\Views;

use View\AbstractView,
	View\Template;

final class Index extends AbstractView
{
	public function executeIndexAction()
	{
		/** Get list of all visitors */
		$this->presentationObjectFactory
			->build('activeVisitors', true)
			->assignData($this->serviceFactory->build('recognition')->getActiveVisitors());	
			
		$this->display('overall_header.html');
		$this->display('overall_navigation.html');
		$this->display('pages/recipes.html');
		$this->display('overall_footer.html');
		
		return true;
	}

	public function executeLogin()
	{	
		if($this->serviceFactory->build('recognition')->getCurrentVisitor()->isLoggedIn())
		{
			return $this->load('indexAction');
		}
		else 
		{
			return $this->display('pages/login.html');		
		}
	}
	
	public function executeLogout()
	{				
		return $this->serviceFactory->build('recognition')->getCurrentVisitor()->isLoggedIn() ? 
					$this->display('pages/logout.html') : 
					$this->display('pages/login.html');
	}



} 