<?php
namespace Controllers\Dev\Rbac;

use App\AbstractController;

final class Roles extends Rbac
{	
	protected function executeAddrbacroles()
	{
		if($this->rbac->Check('DEV_AREA', $this->visitor->user_id)) {
			if($this->request->submit) {
				$dev = $this->serviceFactory->build('dev');

				$array[] = array(
					'title'			=> 'Guest',
					'description'	=> 'This is the guest role111',
					'parent_id'		=> null
				);

				if($dev->addRbacRoles($array)) {
					return $this->load('successfulCommand');
				}
			} else {
				return true;
			}
		} else {
	    	return $this->load('set403error');
		}
	}
}