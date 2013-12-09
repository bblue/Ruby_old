<?php
namespace App\Boot;
use Lib\Functions;
if(IN_CONTROLLER !== true){ die((IS_DEVELOPMENT_AREA === true) ? ('Not in controller. Error at ' . __FILE__ . '.') : ''); }

final class SessionHandler
{
	private $_settings = array(
		'session_expire_time'		=> 3600,
		'use_only_cookies'			=> true,
	);

	public function __construct()
	{
		$this->configureSession();
		$this->startSession();
	}
	
	// Function to retrieve session variables
	public function getVar($varName)
	{
		if(isset($_SESSION[$varName])){
			return $_SESSION[$varName];
		}
		return null;
	}
	
	public function getSessionID()
	{
		return session_id();
	}
	
	// Function to set session variables
	public function setVar($varName, $varVal)
	{
		$_SESSION[$varName] = $varVal;
	}
	
	// Function to end the session
	public function endSession()
	{
		setcookie(session_name(), '', time()-3600);
		session_destroy();
		session_unset();     // unset $_SESSION variable for the run-time 
	}
	
	private function getRemainingSessionTime()
	{
		$timestamp = ($this->getVar('LAST_ACTIVITY') != null) ? $this->getVar('LAST_ACTIVITY') : $this->update_last_activity();
		return $this->_settings['session_expire_time'] - (time() - $timestamp);
	}
	
	public function startSession()
	{
		// Initiate the session
		session_start();

		// Check for session timeout
		if($this->getRemainingSessionTime() < 0)
		{
		    $this->restartSession();
		    return true;
		}
		
		// Attempt to prevent session fixation
		if (!$this->getVar('created'))
		{
			session_regenerate_id();
			$this->setVar('created', true);
		}
		
		// Try to limit the damage from compromised session ID by saving a hash of the User-Agent
		if (!$this->getVar('user_agent'))
		{
			//Create the hash of the user-agent
			$this->setVar('user_agent', md5($_SERVER['HTTP_USER_AGENT']));
		}
		
		if ($this->getVar('user_agent') != md5($_SERVER['HTTP_USER_AGENT']))
		{
			// If we end up here we might have a compromised account
			$this->endSession();
			throw new Exception ('Compromised account. Session closed.');
		}

		// update last activity time stamp
		$this->update_last_activity();

	}
	
	private function update_last_activity()
	{
		return $_SESSION['LAST_ACTIVITY'] = time();
	}
	
	public function restartSession(){
		$this->endSession();
		$this->update_last_activity();
		$this->startSession();
	}
	
	private function configureSession()
	{
		// Change the value of session expire time in php.ini
		ini_set('session.gc_maxlifetime', $this->_settings['session_expire_time']);
		
		// Make sure we only use cookie sessions
		ini_set('session.use_only_cookies', $this->_settings['use_only_cookies']);
		
		// Configure cookie time
		ini_set('session.cookie_lifetime', $this->_settings['session_expire_time']);
	}

	// Disable PHP5's cloning method for session so people can't make copies of the session instance
	public function __clone()
	{
		trigger_error('Clone is not allowed for '.__CLASS__,E_USER_ERROR);
	}
}