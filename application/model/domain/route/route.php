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
		'sCommand',
		'a_id',
		'isRedirect',
		'bCanBypassForcedLogin'
	);
	private $aUrlElements;
	public $iUrlLevels = 0;
	
	const MAX_LEVEL = 3;
	
	const DEFAULT_COMMAND = 'indexAction';
	
	public function getId()
	{
		if(isset($this->_values['id'])) {
	       return $this->_values['id'];
		} else {
		    return 0;
		}
	}
	
	public function extractControllerFromUrl($iLevel = 1)
	{
		$this->dissectUrl();
		return $this->aUrlElements[$iLevel]['sResourceName'];
	}

	public function extractCommandFromUrl($iLevel = 1)
	{
		$this->dissectUrl();
		return $this->aUrlElements[$iLevel]['sCommand'];
	}
	
	public function getUrl()
	{
		if(isset($this->_values['url'])) {
			return $this->_values['url'];
		}
	}
	
	public function getCommand()
	{
		return (!empty($this->_values['sCommand'])) ? $this->_values['sCommand'] : self::DEFAULT_COMMAND;
	}
	
	private function dissectUrl()
	{
		if(!empty($this->aUrlElements)) {
			return $this->aUrlElements;
		} else {
			$parts = explode('/', $this->url);
			
			$this->iUrlLevels = count($parts);
			
			for($i = 1; $i<= self::MAX_LEVEL; $i++) {
				$this->aUrlElements[$i]['sResourceName'] = implode('/', array_slice($parts, 0, $i));
				$this->aUrlElements[$i]['sCommand'] = implode('/', array_slice($parts, $i, 1));
				$this->aUrlElements[$i]['vars'] = array_slice($parts, $i+1);
			}
		}
	}
	
	public function setUrl($url)
	{
		// Save the url
		$this->_values['url'] = $url;
		
		// Dissect the url
		$this->dissectUrl();
	}
	
	public function isEnabled()
	{
	    return true;
		//return isset($this->_values['bIsEnabled']) ? $this->_values['bIsEnabled'] : false; //@todo: Fix this
	}
	
	public function canBypassForcedLogin()
	{
	    return false;
	    //return isset($this->_values['bCanBypassForcedLogin']) ? $this->_values['bCanBypassForcedLogin'] : false; //@todo: fix this
	}	
	
	public function getResourceName()
	{
		if(!empty($this->_values['sResourceName']))
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