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
    	'url'
    );
    
    public function userHasAccess($userID)
    {	
    	return in_array($userID, $this->_values['aUserAccessList']);
    }
    
   	public function usergroupHasAccess($usergroupID)
   	{
   		return in_array($usergroupID, $this->getUsergroupAccessList());
   	}
   	
   	private function getUserAccessList()
   	{
   		if(!isset($this->_values['aUserAccessList']))
   		{
			$this->buildUserAccessList();
   		}
   		return $this->_values['aUserAccessList'];
   	}
   	  	
   	private function getUsergroupAccessList()
   	{
   		if(!isset($this->_values['aUsergroupAccessList']))
   		{
			$this->buildUsergroupAccessList();
   		}
   		return $this->_values['aUsergroupAccessList'];
   	}
   	
   	private function buildUserAccessList()
   	{
   	   	foreach($this->_values['users'] as $user)
	   	{
	   		$this->_values['aUserAccessList'][] = $user->id;
	   	}
	   	return (!empty($this->_values['aUserAccessList'])) ? $this->_values['aUserAccessList'] : array();
   	}
   	
   	private function buildUsergroupAccessList()
   	{
   	   	foreach($this->_values['usergroups'] as $usergroup)
	   	{
	   		$this->_values['aUsergroupAccessList'][] = $usergroup->id;
	   	}
	   	return (!empty($this->_values['aUsergroupAccessList'])) ? $this->_values['aUsergroupAccessList'] : array();
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