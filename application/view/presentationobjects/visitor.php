<?php
namespace View\PresentationObjects;
use View\AbstractPresentationObject, Model\Domain\Visitor\Visitor as VisitorEntity;

final class Visitor extends AbstractPresentationObject
{
	public function assignData(VisitorEntity $visitor)
	{
		$this->assign_vars(array(
			'USERNAME'			=> $visitor->user->Username,
			'FIRSTNAME'			=> $visitor->user->Firstname,
			'IS_GUEST'			=> $visitor->user->isGuest(),
			'USER_ID'			=> $visitor->user_id,
			'IS_LOGGED_IN'		=> $visitor->isLoggedIn(),
		    'REMOTE_ADDR'		=> $visitor->remote_addr,
		));
	}
}