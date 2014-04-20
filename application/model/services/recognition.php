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

	public function getCurrentVisitor()
	{
		if(is_object($this->visitor)) {
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

	public function getUser($iUserID)
	{
		return $this->dataMapperFactory->build('user')->findByID($iUserID);
	}

	public function getUsers($aCriterias = array())
	{
		return $this->dataMapperFactory
			->build('user')
			->find($aCriterias);
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
		// Build a visitor object
		$visitor = $this->getCurrentVisitor();

		// Check if we are already logged in
		if($visitor->isLoggedIn()) {
			$sMessage = 'You are already logged in';
			$this->log->createLogEntry($sMessage, $visitor, 'info', true);
			return true;
		}

		// Check that we have received values
		if(empty($username) || empty($password)) {
			return false;
		}

		// Build a user object
		$user = $this->entityFactory->build('user');

		// Get the requested user from database
		$user->Username = $username;
		$this->dataMapperFactory
			->build('user')
			->fetch($user);

		if($aErrors = $user->hasError()) {
			foreach($aErrors as $sMessage) 	{
				$this->log->createLogEntry($sMessage, $visitor, 'warning', true);
			}
			return false;
		}

		// Make sure user exists
		if(!$user->id) {
			$sMessage = 'Username or password incorrect';
			$this->log->createLogEntry($sMessage, $visitor, 'warning', true);
			return false;
		}

		// Check password
		if(!$user->matchPassword($password)) 	{
			$sMessage = 'Username or password incorrect';
			$this->log->createLogEntry($sMessage, $visitor, 'warning', true);
			return false;
		}

		// Update the entity
		$visitor->user_id = $user->id;
		$visitor->user = $user;

		if(!$visitor->isLoggedIn()) {
			// Something is very wrong...
			throw new \Exception ('Error in user login system');
		}
		//@todo: dersom jeg oppdaterer $visitor->user_id etter å ha hentet $visitor->user så blir det krøll.
		//@todo: Det må IKKE fungerer å $entity->entity2->value = $verdi, da dette ikke vil kunne lagres som det skal. Eventuelt så må jeg lage en funksjon som faktisk vil kunne lagre de sakene, men det virker tungvint...

		if($this->registerVisitor($visitor)) {
			$sMessage = 'You are now logged in as ' . $visitor->user->Firstname;
			$this->log->createLogEntry($sMessage, $visitor, 'success', true);
			return true;
		} else {
			throw \Exception('Registration of visitor with userID ('.$visitor->user_id.') in database failed');
		}
	}

	public function logoutVisitor(Visitor $visitor)
	{
		if($visitor->isLoggedIn()) {
			// Create a new user object
			$user = $this->entityFactory->build('user');
			$user->id = User::GUEST_ID;
			$this->dataMapperFactory
				->build('user')
				->fetch($user);
			$visitor->user_id = User::GUEST_ID;
			$visitor->user = $user;

			if($this->registerVisitor($visitor)) {
				$sMessage = 'You are now signed out';
				$this->log->createLogEntry($sMessage, $this->visitor, 'success', true);
				return true;
			} else {
				throw \Exception('Registration of visitor with userID ('.$visitor->user_id.') in database failed');
			}
		} else {
			$sMessage = 'You are already logged out';
			$this->log->createLogEntry($sMessage, $this->visitor, 'info', true);
			return true;
		}
	}

	// @todo: Lage et decorator pattern på service methods slik at jeg kan logge results automatisk. Eventuelt så må jeg legge til logger i alle methods. Dette kan være det beste, men det krever jo en del repetisjon.
}

//jeg må vurdere under hvilke forhold keg vil registeere og fjerne visitors, og hvilkevisitors det vil være. for instanxe, hva er vitsen med å sende et user ovject dersom jeg likevel skal henteut session id...