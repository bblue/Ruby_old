<?php
namespace Modules;

use View\AbstractView,
	View\Template;

final class DevView extends AbstractView
{	
	protected function executeIndexaction()
	{
		$sTemplateFile = 'dev';
		
		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);
					
		$this->display('custom/header.htm');
		//$this->display('custom/horizontal-nav.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');
	}
	
	protected function executeAddrbacroles()
	{	
		$sTemplateFile = 'rbac/add_role';

		/** Load rbac role tree */
		$this->presentationObjectFactory
			->build('rbac_role_tree', true)
			->assignData();
			
		/** Load rbac permission tree */
		$this->presentationObjectFactory
			->build('rbac_permission_tree', true)
			->assignData();
		
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
	
	protected function executeAddrbacpermissions()
	{
		$sTemplateFile = 'rbac/add_permission';
		
		/** Load rbac role tree */
		$this->presentationObjectFactory
			->build('rbac_role_tree', true)
			->assignData();
			
		/** Load rbac permission tree */
		$this->presentationObjectFactory
			->build('rbac_permission_tree', true)
			->assignData();
			
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
	
	protected function executeResetrbactorubysettings()
	{
		$this->load('Addrbacroles');	
	}
}