<?php
namespace Model\Services;

use
	Model\DomainObjects\UserGroup,
	App\ServiceAbstract,
	Model\Domain\Visitor\Visitor,
	Model\Domain\User\User
;

final class Recognition extends ServiceAbstract
{
	private $visitor;
	
	public function getAppropriateHashCost()
	{
		require_once ROOT_PATH . 'lib/password_compat.php';
		$timeTarget = 0.2; 
		$cost = 9;
		do {
		    $cost++;
		    $start = microtime(true);
		    password_hash("test", PASSWORD_DEFAULT, array('cost' => $cost));
		    $end = microtime(true);
		} while (($end - $start) < $timeTarget);
		
		echo "Appropriate Cost Found: " . $cost . "\n";		
	}
	
	public function getCurrentVisitor()
	{
		if(is_object($this->visitor))
		{
			return $this->visitor;
		} else {
			$visitor = $this->entityFactory->build('visitor');
	
			$this->dataMapperFactory
				->build('session') //@todo: session_status() == PHP_SESSION_ACTIVE
				->fetch($visitor);
	
			$this->dataMapperFactory
				->build('server')
				->fetch($visitor);
	
			$this->dataMapperFactory
				->build('visitor')
				->fetch($visitor);
	
			return $this->visitor = $visitor;		
		}
	}

	public function registerVisitor(Visitor $visitor)
	{
		// Store the visitor to the visitor table
		$visitor->setTimestamp();
		return $this->dataMapperFactory
			->build('visitor')
			->store($visitor);
	}
	
	public function removeVisitor(Visitor $visitor)
	{
		return $this->dataMapperFactory
			->build('visitor')
			->delete($visitor);
	}

	public function getActiveVisitors()
	{
        $aCriterias['timestamp'][] = array(
        	'operator'	=> '>',
        	'value'		=> time() - (60*5)
        );
		return $this->dataMapperFactory
			->build('visitor')
			->find($aCriterias);
	}
	
	/* Function to log in a user */
	public function authenticate($username, $password)
	{
		// Check that we have received values
		if(empty($username) || empty($password))
		{
			return false;
		}
		
		// Build a user object
		$user = $this->entityFactory->build('user');
		
		// Get the requested user from database
		$user->Username = $username;
		$this->dataMapperFactory
			->build('user')
			->fetch($user);

		// Build a visitor object
		$this->visitor = $this->getCurrentVisitor();
		
		if($aErrors = $user->hasError())
		{
			foreach($aErrors as $sMessage)
			{
				$this->log->createLogEntry($sMessage, $this->visitor, 'warning', true);
			}
			return false;
		}
				
		// Make sure user exists
		if(!$user->id)
		{
			$sMessage = 'Username or password incorrect';
			$this->log->createLogEntry($sMessage, $this->visitor, 'warning', true);
			return false;
		}
			
		// Check password
		if(!$user->matchPassword($password))
		{
			$sMessage = 'Username or password incorrect';
			$this->log->createLogEntry($sMessage, $this->visitor, 'warning', true);
			return false;
		}
		
		// Update the entity
		$this->visitor->user_id = $user->id;
		$this->visitor->user = $user;
		
		//@todo: dersom jeg oppdaterer $visitor->user_id etter å ha hentet $visitor->user så blir det krøll.
		//@todo: Det må IKKE fungerer å $entity->entity2->value = $verdi, da dette ikke vil kunne lagres som det skal. Eventuelt så må jeg lage en funksjon som faktisk vil kunne lagre de sakene, men det virker tungvint...
		
		if($this->registerVisitor($this->visitor))
		{
			$sMessage = 'You are now logged in as ' . $this->visitor->user->Firstname;
			$this->log->createLogEntry($sMessage, $this->visitor, 'success', true);
			return true;
		} else {
			throw \Exception('Unable to register logged in user'); 
		}
	}
	
	public function logoutVisitor(Visitor $visitor)
	{
		if($visitor->isLoggedIn())
		{
			$visitor->user_id = User::GUEST_ID;
			$this->dataMapperFactory
				->build('visitor')
				->store($visitor);
			$sMessage = 'Successful sign out';
			$this->log->createLogEntry($sMessage, $this->visitor, 'success', true);
		}
		else 
		{
			$sMessage = 'You are already logged out';
			$this->log->createLogEntry($sMessage, $this->visitor, 'info', true);
		}
	}
	
	// @todo: Lage et decorator pattern på service methods slik at jeg kan logge results automatisk. Eventuelt så må jeg legge til logger i alle methods. Dette kan være det beste, men det krever jo en del repetisjon.
}

//jeg må vurdere under hvilke forhold keg vil registeere og fjerne visitors, og hvilkevisitors det vil være. for instanxe, hva er vitsen med å sende et user ovject dersom jeg likevel skal henteut session id...