<?php
namespace Model\Domain\Visitor;
use Model\Domain\User\User;
use Lib\Mobile_Detect;
use App\AbstractEntity;
use App\CollectionProxy;

final class Visitor extends AbstractEntity
{  
    protected $_allowedFields = array(
	    'id',
	    'timestamp',
	    'controller',
	    'method',
	    'user_id',
	    'http_user_agent',
	    'remote_addr',
	    'user',
	    'http_vars',
    	'device',
    	'platform',
    	'browser'
    );
    
    public function setId($id)
    {
        $this->_values['id'] = $id;
    }
	public function getUser_id()
	{
		return isset($this->_values['user_id']) ? $this->_values['user_id'] : User::GUEST_ID; 
	}
	
    public function setUser_id($id)
    {
    	// Remove the user if user ID changes
    	if(is_object($this->_values['user']))
    	{
    		$this->_values['user'] = false;
    	}
    	
    	$this->_values['user_id'] = isset($id) ? (int)$id : User::GUEST_ID; 
    }
    
    public function setHttp_user_agent($http_user_agent)
    {
    	$this->_values['http_user_agent'] = $http_user_agent;
    }
    
    public function setRemote_addr($remote_addr)
    {
    	$this->_values['remote_addr'] = $remote_addr;
    }
    
    public function setController($sController)
    {
    	$this->_values['sController'] = $sController;
    }
    
    public function setMethod($sMethod)
    {
    	$this->_values['sMethod'] = $sMethod;
    }
    
    public function setTimestamp($timestamp = null)
    {
    	$this->_values['timestamp'] = ($timestamp)?:time();
    }
    
    public function setUser(CollectionProxy $user)
    {
        $this->_values['user'] = $user;
    }
    
    public function getUser()
    {
    	if(is_object($this->_values['user']))
    	{
    		return $this->_values['user']->getEntity();
    	}
    	throw new \Exception('User is not set for userID=' . $this->user_id);
    }
    
    public function getDevice($recalc = false)
    {
    	if(!isset($this->_values['device']) || $recalc) 
    	{
    		$this->setDevice(null);
    	}
    	return $this->_values['device'];
    }
    
    protected function setDevice($device)
    {
     	if($device) 
    	{
    		$this->_values['device'] = $device;
    	} else {
    		$mobileDetect = $this->getMobileDetect();
    		$this->_values['device'] = (($device = $mobileDetect->getPhoneDevice()) ? $device . ' (Mobile phone)' : (($device = $mobileDetect->getTabletDevice()) ? $device . ' (Tablet)' : 'Desktop computer'));
    	}
    	return $this->_values['device'];
    }
    
    public function getPlatform($recalc = false)
    {
    	if(isset($this->_values['platform']) || $recalc) 
    	{
    		$this->setPlatform(null);
    	} 
    	return $this->_values['platform'];
    }
    
    protected function setPlatform($platform)
    {
     	if($platform) 
    	{
    		$this->_values['platform'] = $platform;
    	}
    	else 
    	{
	    	$this->_values['platform'] = $this->getMobileDetect()->getPlatform() ? : 'Unknown OS';  
    	}
    	return $this->_values['platform'];
    }
    
    public function getBrowser($recalc = false)
    {
    	if(isset($this->_values['browser']) || $recalc) 
    	{
    		$this->setBrowser(null);
    	}
    	return $this->_values['browser'];
    }
    
	protected function setBrowser($browser)
	{
		if($browser) 
    	{
    		$this->_values['browser'] = $browser;
    	}
    	else {	
	    	$this->_values['browser'] = $this->getMobileDetect()->getBrowser() ? : 'Unknown browser';   	
    	}
    	return $this->_values['browser'];
	} 
    
    private function getMobileDetect()
    {
     	if(!$this->http_user_agent)
    	{
    		throw new \Exception('User agent not set for visitor');
    	}
        if(!$this->http_vars)
    	{
    		throw new \Exception('HTTP vars not set for visitor');
    	}
    	$mobileDetect = new Mobile_Detect();
    	
    	$mobileDetect->setUserAgent($this->http_user_agent);
    	
    	$mobileDetect->setHttpHeaders(unserialize($this->http_vars));
    	
    	return $mobileDetect;
    } 
    
    ###################################### Methods ###################################### 
    public function isLoggedIn()
    {
    	if($this->getUser_id() != User::GUEST_ID)
    	{
    		return true;
    	}
    	return false;
    }
}