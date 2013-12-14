<?php
namespace Model\Domain\Route;

use App\CollectionProxy,
	App\AbstractEntity;

final class Route extends AbstractEntity
{  
	protected $_allowedFields = array(
		'id',
		'aUserAccessList',
		'aUsergroupAccessList',
		'users',
		'usergroups',
		'url',
		'sResourceName',
		'bIsEnabled',
		'sCommand'
	);
	
	const DEFAULT_COMMAND = 'indexAction';
	
	public function getUrl()
	{
		if(isset($this->_values['url']))
		{
			return $this->_values['url'];
		}
	}
	
	public function getCommand()
	{
		return (!empty($this->_values['sCommand'])) ? : self::DEFAULT_COMMAND;
	}
	
	public function setUrl($url)
	{
		// save the url
		$this->_values['url'] = $url;
	}
	
	public function isEnabled()
	{
		return $this->_values['bIsEnabled'];
	}
	
	public function getResourceName()
	{
		if(isset($this->_values['sResourceName']))
		{
			return $this->_values['sResourceName'];
		}
		throw new \Exception('Resource name is unset');
	}
	
	public function setResourceName($sResourceName)
	{
		$this->_values['sResourceName'] = $sResourceName;
	}
	
	public function userHasAccess($userID)
	{	
		return in_array($userID, $this->getUserAccessList());
	}

   	public function usergroupHasAccess($usergroupID)
   	{
		return in_array($usergroupID, $this->getUsergroupAccessList());
   	}
   	
   	private function getUserAccessList()
   	{
   		if(!isset($this->_values['aUserAccessList']))
   		{
			$this->_values['aUserAccessList'] = $this->buildUserAccessList();
   		}
   		return $this->_values['aUserAccessList'];
   	}
   	  	
   	private function getUsergroupAccessList()
   	{
   		if(!isset($this->_values['aUsergroupAccessList']))
   		{
			$this->_values['aUsergroupAccessList'] = $this->buildUsergroupAccessList();
   		}
   		return $this->_values['aUsergroupAccessList'];
   	}
   	
   	private function buildUserAccessList()
   	{
		$users = $this->getUsers();
		$array = array();
   	   	foreach($users as $user)
	   	{
	   		$array[] = $user->id;
	   	}
	   	return $array;
   	}
   	
   	private function buildUsergroupAccessList()
   	{
		$usergroups = $this->getUsergroups();
		$array = array();
   	   	foreach($usergroups as $usergroup)
	   	{
	   		$array[] = $usergroup->id;
	   	}
	   	return $array;
   	}
   	  	
   	private function getUsergroups()
   	{
		return ($this->_values['usergroups']) ? : array();
   	}
   	
   	private function getUsers()
   	{
   		return ($this->_values['users']) ? : array();
   	}

   	public function setUsergroups(CollectionProxy $usergroups)	
	{
		$this->_values['usergroups'] = $usergroups;
	}
	
	public function setUsers(CollectionProxy $users)	
	{
		$this->_values['users'] = $users;
	}
}