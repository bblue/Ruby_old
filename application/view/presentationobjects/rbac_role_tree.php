<?php
namespace View\PresentationObjects;

use Model\Domain\Log\Log as LogEntry;

use View\AbstractPresentationObject;

final class Rbac_role_tree extends AbstractPresentationObject
{
	
	private $rbac;
	public function assignData($rbac, $startID = 1)
	{
		$this->rbac = $rbac;
		if($startID == 1) {
			$startID = $rbac->Roles->TitleID('root');
		}

		$aRoles = $rbac->Roles->Children($startID);
		
		foreach($aRoles as $aRole) {
			$finalstring .= $this->getRole($aRole);
		}
		$this->template->assign_var('TEST', "<ul>$finalstring</ul>\n");
	}
	
	private function _prepare_role_line($aRole)
	{
		return '<a href="#" title="'.$aRole['Description'].'" data-node-id="'.$aRole['ID'].'">'.$aRole['Title']."</a>\n";
	}
	
	private function _get_role_children($id)
	{
		return $this->rbac->Roles->Children($id);
	}

	private function getRole($parent)
	{
			$str = '<li>'.$this->_prepare_role_line($parent);
			
			$children = $this->_get_role_children($parent['ID']);
			if(!empty($children)) {
				$str .= '<ul>';
				foreach($children as $role) {
					$str .= $this->getRole($role);
				}
				$str .= "</ul>\n";
			}
			return $str .= "</li>\n";
	}
	
}