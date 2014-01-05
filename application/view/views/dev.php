<?php
namespace View\Views;

use View\AbstractView,
	View\Template;

final class Dev extends AbstractView
{	
	protected function executeIndexaction()
	{
		$sTemplateFile = 'dev';
			
		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');
		return true;
	}
	
	protected function executeAddrbacroles()
	{
		return $this->load('indexaction');	
	}
	protected function executeAddrbacpermissions()
	{
		return $this->load('indexaction');	
	}
	
	protected function executeRbac()
	{
		return $this->load('indexaction');	
	}
}