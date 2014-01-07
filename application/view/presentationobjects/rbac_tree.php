<?php
namespace View\PresentationObjects;

use View\AbstractPresentationObject;

abstract class Rbac_tree extends AbstractPresentationObject
{
	protected $rbac;
	
	public function assignData($iStartID = 1)
	{
		// Save the rbac object
		$this->rbac = new \PhpRbac\Rbac();
		
		// Get the title ID if we start at root
		if($iStartID == 1) {
			$iStartID = $this->getElementID('root');
		}

		// Get the children of the start ID
		$aElements = $this->getChildren($iStartID);
		
		// Create the nested roles
		foreach($aElements as $aElement) {
			$sTree .= $this->getElement($aElement);
		}
		
		return $this->assign($sTree);
	}
	
	protected function prepareElementLine($aElement)
	{
		return '<a href="#" data-node-id="'.$aElement['ID'].'"><span title="'.$aElement['Description'].'">'.$aElement['Title']."</span></a>";
		//title="'.$aElement['Description'].'"
	}
	
	protected function getElement($parent)
	{
			$str = "<li>".$this->prepareElementLine($parent);		
			$children = $this->getChildren($parent['ID']);
			if(!empty($children)) {
				$str .= "<ul>\n";
				foreach($children as $element) {
					$str .= $this->getElement($element);
				}
				$str .= "</ul>\n";
			}
			return $str .= "</li>\n";
	}
	
	protected abstract function assign($sTree);
	protected abstract function getElementID($sTitle);
	protected abstract function getChildren($id);
}