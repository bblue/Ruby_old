<?php
namespace Modules\Dev\Rbac;

use Modules\Dev\RbacController;

final class PermissionsController extends RbacController
{
	protected function executeAddrbacpermissions()
	{
		if($this->rbac->Check('DEV_AREA', $this->visitor->user_id)) {
			if($this->request->submit) {
				$dev = $this->serviceFactory->build('dev');

				$array[] = array(
					'title'			=> 'Permission',
					'description'	=> 'This is a permission',
					'parent_id'		=> $this->request->parent_id
				);

				if($dev->addRbacPermissions($array)) {
					return $this->load('successfulCommand');
				}
			} else {
				// Standard view for this command
				return true;
			}
		} else {
	    	return $this->load('set403error');
		}
	}
}