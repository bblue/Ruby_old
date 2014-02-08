<?php
namespace View\Views;

use View\AbstractView,
	View\Template;

final class Recipes extends AbstractView
{
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
	
	public function executeAdd()
	{
		$sTemplateFile = 'recipes/add';
		
		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);
			
		/** Load breadcrumbs */
		$this->presentationObjectFactory
			->build('breadcrumbs', true)
			->assignData(array(
				array(
					'title'	=> 'Recipes',
				),				
				array(
					'url'	=> 'recipes/add',
					'title'	=> 'Add New Recipe',
				)
			));
								
		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');		
	}
	
	public function executeValidate()
	{
		header('Content-type: application/json');
		echo json_encode(true);
	}
	
	protected function executeGetcategories()
	{
		header('Content-type: application/json');
		echo json_encode(array('red', 'green', 'blue', 'yellow'));
	}
} 