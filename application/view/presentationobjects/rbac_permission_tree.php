<?php
namespace View\PresentationObjects;

use View\AbstractPresentationObject;

final class Rbac_permission_tree extends Rbac_tree
{
	protected function assign($sTree)
	{
		$this->template->assign_var('RBAC_PERMISSION_TREE', "<ul>$sTree</ul>\n");	
	}
	
	protected function getElementID($sTitle)
	{
		return $this->rbac->Permissions->TitleID($sTitle);
	}
	
	protected function getChildren($id)
	{
		return $this->rbac->Permissions->Children($id);
	}
	
}