<?php
namespace Modules;

use App\AbstractView,
	App\Template;

final class IndexView extends AbstractView
{
	public function executeIndexaction()
	{
		$sTemplateFile = 'index';
		
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
} 