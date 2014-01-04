<?php
namespace Controllers;

use App\AbstractController;

final class Dev extends AbstractController
{	
	public function indexAction()
	{
		return $this->addRbacRoles();	
	}
	
	public function addRbacRoles()
	{
		$dev = $this->serviceFactory->build('dev');
		
		$array[] = array(
			'title'			=> 'Guest',
			'description'	=> 'This is the guest role',
			'parent_id'		=> null
		);
		
		$visitor = $this->serviceFactory->build('recognition', true)->getCurrentVisitor();
		
		$dev->addRbacRoles($array, $visitor);
		
		return true;
	}
	
	public function addRbacPermissions()
	{
		$dev = $this->serviceFactory->build('dev');
		
		$array[] = array(
			'title'			=> 'Permission',
			'description'	=> 'This is a permission',
			'parent_id'		=> null
		);
		
		$visitor = $this->serviceFactory->build('recognition', true)->getCurrentVisitor();
		
		$dev->addRbacPermissions($array, $visitor);
		
		return true;
	}
}