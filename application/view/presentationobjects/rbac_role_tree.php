<?php
namespace View\PresentationObjects;

final class Rbac_role_tree extends Rbac_tree
{
	protected function assign($sTree)
	{
		$this->template->assign_var('RBAC_ROLE_TREE', "<ul>$sTree</ul>\n");	
	}
	
	protected function getElementID($sTitle)
	{
		return $this->rbac->Roles->TitleID($sTitle);
	}
	
	protected function getChildren($id)
	{
		return $this->rbac->Roles->Children($id);
	}
}