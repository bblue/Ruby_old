<?php
namespace View\PresentationObjects;

use Model\Domain\Visitor\Visitor as VisitorEntity;

use View\AbstractPresentationObject;

final class Nav_userinfo extends AbstractPresentationObject
{
	public function assignData(VisitorEntity $visitor)
	{
		$this->template->set_filenames(array(
			'nav_userinfo' 				=> (($visitor->isLoggedIn()) ? 'blocks/nav_user_info.htm' : 'blocks/nav_guest_info.htm'),
		));

		$this->template->assign_display('nav_userinfo', 'NAV_USER_INFO', true);
	}
}