<?php
namespace Controllers;

use App\AbstractController;

final class Users extends AbstractController
{
	public function indexAction()
	{
		return $this->view();
	}
	
	public function login()
	{
		$this->serviceFactory
			->build('recognition')
			->authenticate($this->request->username, $this->request->password);
		return true;
	}
	
	public function logout()
	{
		$recognition = $this->serviceFactory->build('recognition');
		$recognition->logoutVisitor($recognition->getCurrentVisitor());
		return true;
	}
	
	public function view()
	{
		if(!empty($this->request->u_id))
		{
			// Get user ID
		} else {
			// Get all users based on filter and search criteria
			
		}
		return true;
	}
	
	public function register()
	{
		return true;
	}
	
	public function passwordReset()
	{
		return true;
	}
	
	public function inbox()
	{
		return true;
	}
	
	public function timeline()
	{
		return true;
	}
	
	public function adduser()
	{
		return true;
	}
}