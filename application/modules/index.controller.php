<?php
namespace Modules;

use App\AbstractController;

final class IndexController extends AbstractController
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