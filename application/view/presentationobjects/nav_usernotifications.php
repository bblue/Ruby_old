<?php
namespace View\PresentationObjects;

use Model\Domain\Visitor\Visitor as VisitorEntity;

use View\AbstractPresentationObject;

final class Nav_usernotifications extends AbstractPresentationObject
{
	public function assignData(VisitorEntity $visitor)
	{
		if($visitor->isLoggedIn())
		{
			$this->template->set_filenames(array(
				'nav_usernotifications' 	=> 'blocks/nav_user_notifications.htm',
			));
			
			$this->assign_display('nav_usernotifications', 'NAV_USER_NOTIFICATIONS', true);
		} 
		else
		{
			$this->assign_var('NAV_USER_NOTIFICATIONS', null);
		}
	}
}