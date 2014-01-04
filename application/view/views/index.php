<?php
namespace View\Views;

use View\AbstractView,
	View\Template;

final class Index extends AbstractView
{
	public function executeIndexAction()
	{
		$sTemplateFile = 'index';

		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display($sTemplateFile . '.htm');
		$this->display('custom/footer.htm');
		
		return true;
	}
} 