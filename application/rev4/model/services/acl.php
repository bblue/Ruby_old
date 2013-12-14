<?php
namespace Model\Services;

use Model\Domain\Usergroup\Collection as Usergroups,
	Model\Domain\Usergroup\Usergroup,
	Model\Domain\User\User;

use App\ServiceAbstract,
	App\CollectionProxy;

use Model\Domain\Visitor\Visitor,
	Model\Domain\Route\Route;

final class ACL extends ServiceAbstract
{
	public function visitorHasAccess(Visitor $visitor, Route $route)
	{
		if($this->visitorIsBlocked($visitor))
		{
			return false;
		}

		if($this->testUserAccess($visitor->user, $route))
		{
			return true;
		}

		if($this->testUsergroupsAccess($visitor->user->usergroups, $route))
		{
			return true;
		}
	}

	public function visitorIsBlocked(Visitor $visitor)
	{
		return $this->testVisitorBlock($visitor);
	}
	
	private function testUsergroupsAccess($usergroups, Route $route)
	{
		if($usergroups instanceof Usergroups || $usergroups instanceof CollectionProxy)
		foreach($usergroups as $usergroup)
		{
			if($route->usergroupHasAccess($usergroup->id))
			{
				return true;
			}
		}
	}

	private function testUserAccess(User $user, Route $route)
	{
		$route->userHasAccess($user->id);
	}
	
	private function testVisitorBlock(Visitor $visitor)
	{
		// Test if IP is blocked
		
		// Test if user is blocked		
	}
}