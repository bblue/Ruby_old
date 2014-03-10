<?php
namespace Modules;

use App\AbstractController;

final class DevController extends AbstractController
{	
	protected function executeIndexaction()
	{
		if($this->rbac->Check('VIEW_DEV_AREA', $this->visitor->user_id)) {
			return true;
		} else {
			return $this->load('set403error');
		}
	}
}