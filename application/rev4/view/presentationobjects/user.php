<?php
namespace View\PresentationObjects;
use View\AbstractPresentationObject, Model\Domain\User\User as UserEntity;

final class User extends AbstractPresentationObject
{
	public function assignData(UserEntity $user)
	{
		$this->assign_vars(array(
			'USERNAME'			=> $user->Username,
			'IS_GUEST'			=> $user->isGuest(),
			'IS_ADMIN'			=> $user->isAdmin(),
			'MAIN_USERGROUP'	=> $user->MainUsergroup,
		));
		foreach($user->usergroups as $usergroup)
		{
			$this->assign_block_vars('usergroups', array(
				'ID' 			=> $usergroup->id,
			));			
		}	
	}
}