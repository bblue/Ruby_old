<?php
namespace Modules\Dev;

use App\AbstractView,
	App\Template;

class RbacView extends AbstractView
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
}