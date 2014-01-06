<?php
namespace View\Views;

use View\AbstractView,
	View\Template;

final class Dev extends AbstractView
{	
	protected function executeIndexaction()
	{
		$sTemplateFile = 'form-wizard';

		$rbac = new \PhpRbac\Rbac();
		/** Load role org chart */
		$this->presentationObjectFactory
			->build('rbac_role_tree', true)
			->assignData($rbac);
			
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
	
	protected function executeAddrbacroles()
	{
		$sTemplateFile = 'dev';
		
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
	protected function executeAddrbacpermissions()
	{
		return $this->load('Addrbacroles');	
	}
	
	protected function executeRbac()
	{
		return $this->load('Addrbacroles');	
	}
}