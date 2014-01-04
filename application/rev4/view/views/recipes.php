<?php
namespace View\Views;

use View\AbstractView,
	View\Template;

final class Recipes extends AbstractView
{
	public function executeIndexAction()
	{
		return $this->load('managemyrecipes');
	}
	
	public function executeManagemyrecipes()
	{
		$sTemplateFile = 'managemyrecipes';
					
		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/recipes/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');
		
		return true;
	}
	
} 