<?php
namespace Model\Domain\User;
use App\AbstractEntity;
use App\CollectionProxy;

final class User extends AbstractEntity
{
	protected $_allowedFields = array(
		'id',
		'Firstname',
		'Lastname',
		'Email',
		'IsActivated',
		'Password',
		'NeedsRevalidation',
		'Username',
		'recipes',
		'SecretAnswer',
		'SecretQuestion',
		'MainUsergroup',
		'VerificationCodeExpireTime',
		'VerificationCode',
		'RegistrationKey',
		'VerificationDate',
		'RegistrationDate',
		'RevalidationCode',
		'RevalidationCodeExpireTime',
		'RevalidationDate',
		'IsLocked',
		'isAdmin',
		'GUEST_ID'
	);

	const GUEST_ID = 0;
	const PASSWORD_HASH_COMPLEXITY = PASSWORD_HASH_COMPLEXITY;

	public function matchPassword($password)
	{
		require_once ROOT_PATH . DIRECTORY_SEPARATOR  . 'lib'.DIRECTORY_SEPARATOR.'password_compat.php';
		if (password_verify($password, $this->_values['Password'])) {
			if (password_needs_rehash($this->_values['Password'], PASSWORD_DEFAULT, array('cost' => self::PASSWORD_HASH_COMPLEXITY))) {
				$this->setPassword($this->hashPassword($password));
			}
			return true;
		}
		return false;
	}

	private function hashPassword($password)
	{
		require_once ROOT_PATH .DIRECTORY_SEPARATOR. 'lib'.DIRECTORY_SEPARATOR.'password_compat.php';
		return password_hash($password, PASSWORD_DEFAULT, array('cost' => self::PASSWORD_HASH_COMPLEXITY));
	}

	public function setPassword($password)
	{
		$this->_values['Password'] = $password;
	}

	public function getPassword()
	{
		throw new \Exception('Password may not be extracted');
	}

	public function isGuest()
	{
		return ((self::GUEST_ID === $this->id) ? true : false);
	}

	public function isAdmin()
	{
		if($this->isGuest()) {
			return false;
		}

		if(isset($this->_values['isAdmin'])) {
			return $this->_values['isAdmin'];
		}

		return $this->_values['isAdmin'] = false;
	}

	/**
	 * Set the user's ID
	 */
	public function setId($id)
	{
		unset($this->_values['isAdmin']);
		settype($id, 'int');
		$this->_values['id'] = $id;
	}

	/**
	 * Set the user's first name
	 */
	public function setFirstname($fname)
	{
		if (strlen($fname) < 2 || strlen($fname) > 32) {
			throw new \Exception('The specified first name is invalid.');
		}
		$this->_values['Firstname'] = $fname;
	}

	/**
	 * Set the user's last name
	 */
	public function setLastname($lname)
	{
		$this->_values['Lastname'] = $lname;
	}

	/**
	 * Set the user's username
	 */
	public function setUsername($uname)
	{
		if ((strlen($uname) < 2 || strlen($uname) > 32) && $uname !== null) {
			throw new \Exception('The specified last name is invalid.');
		}
		$this->_values['Username'] = $uname;
	}

	/**
	 * Set the user's email address
	 */
	public function setEmail($email)
	{
		if($email) //@todo: dersom null statement burde jeg filtere et annet sted
		{
			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				throw new \Exception('The specified email address is invalid.');
			}
		}
		$this->_values['Email'] = $email;
	}

	/**
	 * Set the comments of the entry (assigns a collection proxy for lazy-loading comments)
	 */
	public function setRecipes(CollectionProxy $recipes)
	{
		$this->_values['recipes'] = $recipes;
	}

	public function setUsergroups(CollectionProxy $usergroups)
	{
		$this->_values['usergroups'] = $usergroups;
	}

	public function setIsAdmin($bIsAdmin)
	{
		$this->_values['isAdmin'] = $bIsAdmin;
	}

}