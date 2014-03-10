<?php
namespace Controllers;

use App\AbstractController;

final class Index extends AbstractController
{
	protected function executeIndexaction()
	{
		if($this->rbac->Check('VIEW_DASHBOARD', $this->visitor->user_id)) {
			
			return true;
		} else {
			return $this->load('set403error');
		}
	} 

}