<?php
namespace View\PresentationObjects;

use Model\Domain\Visitor\Visitor as VisitorEntity;

use View\AbstractPresentationObject;

final class Nav_userinbox extends AbstractPresentationObject
{
	public function assignData(VisitorEntity $visitor)
	{
		if($visitor->isLoggedIn())
		{
			$this->template->set_filenames(array(
				'nav_userinbox' 			=> 'blocks/nav_user_inbox.htm'
			));
			
			$this->assign_display('nav_userinbox', 'NAV_USER_INBOX', true);	
		} 
		else
		{
			$this->assign_var('NAV_USER_INBOX', null);
		}
	}
}