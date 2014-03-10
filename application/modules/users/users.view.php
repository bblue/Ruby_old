<?php
namespace Modules;

use View\AbstractView,
	View\Template;

final class UsersView extends AbstractView
{
	public function executeIndexAction()
	{
		return $this->load('view');
	}
	
	public function executeLogin()
	{
		$visitor = $this->serviceFactory->build('recognition', true)->getCurrentVisitor();
		
		$sTemplateFile = ($visitor->isLoggedIn()) ? 'extras-blank' : 'extras-login';
		
		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);
		
		if($visitor->isLoggedIn()) {
			$this->display('custom/header.htm');
			$this->display('custom/sidebar.htm');
			$this->display('custom/rightbar.htm');
			$this->display('custom/' . $sTemplateFile . '.htm');
			$this->display('custom/footer.htm');
		} else {
			$this->display('custom/'.$sTemplateFile.'.htm');
		}
		
		return true;
	}
	
	public function executeLogout()
	{			
		if($this->serviceFactory->build('recognition', true)->getCurrentVisitor()->isLoggedIn())
		{
			$sTemplateFile = 'extras-blank';
		
			/** Load required scripts */
			$this->presentationObjectFactory
				->build('scripttags', true)
				->assignData($sTemplateFile);
							
			$this->display('custom/header.htm');
			$this->display('custom/sidebar.htm');
			$this->display('custom/rightbar.htm');
			$this->display('custom/' . $sTemplateFile . '.htm');
			$this->display('custom/footer.htm');
		}
		else 
		{
			$this->display('custom/extras-login.htm');
		}
		
		return true;
	}
	
	public function executeView()
	{	
		// Get the users service and check if we have one or many users
		
		$sTemplateFile = 'extras-profile';
				
		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);
					
		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');
		
		return true;
	}
	
	public function executeRegister()
	{
		$sTemplateFile = 'extras-signupform';
				
		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);
			
		$this->display('custom/' . $sTemplateFile . '.htm');
		
		return true;
	}
	
	public function executePasswordreset()
	{
		$sTemplateFile = 'extras-forgotpassword';
				
		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);
			
		$this->display('custom/' . $sTemplateFile . '.htm');
		
		return true;
	}
	
	public function executeInbox()
	{
		$sTemplateFile = 'extras-inbox';
					
		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);
					
		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');
		
		return true;
	}
	
	public function executeTimeline()
	{
		$sTemplateFile = 'extras-timeline';
					
		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);
					
		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');
		
		return true;
	}
	
	public function executeAdduser()
	{
		$sTemplateFile = 'extras-registration';
					
		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);
			
		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');
		
		return true;		
	}
	
} 