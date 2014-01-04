<?php
namespace View\Views;

use View\AbstractView,
	View\Template;

final class Dev extends AbstractView
{	

	public function executeAddrbacroles()
	{
		$sTemplateFile = 'dev';
			
		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');	
		
		return true;
	}
	public function executeAddrbacpermissions()
	{
		$this->load('addrbacroles');	
		
		return true;
	}
}