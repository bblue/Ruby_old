<?php
namespace View\Views;

use View\AbstractView,
	View\Template;

final class Users extends AbstractView
{
	public function executeIndexAction()
	{
		return $this->load('view');
	}
	
	public function executeLogin()
	{
		$visitor = $this->serviceFactory->build('recognition', true)->getCurrentVisitor();
		if($visitor->isLoggedIn()) {
			$sTemplateFile = 'extras-blank';
			
			$this->display('custom/header.htm');
			$this->display('custom/sidebar.htm');
			$this->display('custom/rightbar.htm');
			$this->display('custom/' . $sTemplateFile . '.htm');
			$this->display('custom/footer.htm');
		} else {
			$this->display('custom/extras-login.htm');
		}
		
		return true;
	}
	
	public function executeLogout()
	{			
		if(!$this->serviceFactory->build('recognition', true)->getCurrentVisitor()->isLoggedIn())
		{
			$sTemplateFile = 'extras-blank';
			
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
		
		$this->display('custom/' . $sTemplateFile . '.htm');
		
		return true;
	}
	
	public function executePasswordreset()
	{
		$sTemplateFile = 'extras-forgotpassword';
		
		$this->display('custom/' . $sTemplateFile . '.htm');
		
		return true;
	}
	
	public function executeInbox()
	{
		$sTemplateFile = 'extras-inbox';
					
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
			
		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');
		
		return true;		
	}
	
} 