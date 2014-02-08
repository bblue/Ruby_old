<?php
namespace View\PresentationObjects;
use View\AbstractPresentationObject;

final class Breadcrumbs extends AbstractPresentationObject
{
	public function assignData(array $aPath)
	{
		array_unshift($aPath, array(
			'url'	=> '/',
			'title'	=> 'Dashboard'
		));
		$aLastElement = end($aPath);
		
		foreach($aPath as $aPathElement) {
			$this->template->assign_block_vars('breadcrumbs', array(
				'URL'		=> $aPathElement['url'],
				'TITLE'		=> $aPathElement['title'],
				'IS_ACTIVE'	=> ($aPathElement['title'] == $aLastElement['title']) 
			));	
		}
		
		$this->template->set_filenames(array(
			'breadcrumbs' 			=> 'blocks/breadcrumbs.htm'
		));
		
		$this->assign_display('breadcrumbs', 'BREADCRUMBS', true);
	}
}