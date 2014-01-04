<?php
namespace Model\Services;

use App\ServiceAbstract;

use Model\Domain\Visitor\Visitor;

final class Dev extends ServiceAbstract
{
	public function addRbacRoles(array $aRoles, $visitor)
	{
		$rbac = new \PhpRbac\Rbac();
		
		foreach($aRoles as $aRole)
		{
			if($id = $rbac->Roles->Add($aRole['title'], $aRole['description'], $aRole['parent_role_id']))
			{
				$sMessage = 'Insert role (#'.((isset($aRole['parent_id'])) ? ($aRole['parent_id'] . '-') : '')."<b>$id</b>): ". $aRole['title']. ' <i>[' . $aRole['description'] . ']</i>';
				$this->log->createLogEntry($sMessage, $visitor, 'info', true);
			}
		}	
		return true;
	}
	
	public function addRbacPermissions(array $aPermissions, $visitor)
	{
		$rbac = new \PhpRbac\Rbac();
		
		foreach($aPermissions as $aPermission)
		{
			if($id = $rbac->Permissions->Add($aPermission['title'], $aPermission['description'], $aPermission['parent_role_id']))
			{
				$sMessage = 	'Insert permission (#'.((isset($aPermission['parent_id'])) ? ($aPermission['parent_id'] . '-') : '')."<b>$id</b>): ". $aPermission['title']. ' <i>[' . $aPermission['description'] . ']</i>';
				$this->log->createLogEntry($sMessage, $visitor, 'info', true);
			}
		}
		return true;
	}
	
}