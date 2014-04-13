<?php
namespace Modules\Dev\Rbac;

use Modules\Dev\RbacController;

final class RolesController extends RbacController
{
	protected function executeAddrbacroles()
	{
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
		}
	}
}