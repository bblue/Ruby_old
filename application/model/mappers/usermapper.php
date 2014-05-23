<?php
namespace Model\Mappers;
use
	Model\Domain\User\User,
	App\DatabaseDataMapper,
	App\AbstractEntity,
	App\CollectionProxy;

final class UserMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'User';
	protected $_acceptedFields = array(
		'id'						=> 'users.id',
		'Firstname'					=> 'users.Firstname',
		'Lastname'					=> 'users.Lastname',
		'Email'						=> 'users.Email',
		'IsActivated'				=> 'users.IsActivated',
		'Password'					=> 'users.Password',
		'NeedsRevalidation'			=> 'users.NeedsRevalidation',
		'Username'					=> 'users.Username',
		'SecretAnswer'				=> 'users.SecretAnswer',
		'SecretQuestion'			=> 'users.SecretQuestion',
		'MainUsergroup'				=> 'users.MainUsergroup',
		'VerificationCodeExpireTime'=> 'users.VerificationCodeExpireTime',
		'VerificationCode'			=> 'users.VerificationCode',
		'VerificationDate'			=> 'users.VerificationDate',
		'RegistrationKey'			=> 'users.RegistrationKey',
		'RevalidationCode'			=> 'users.RevalidationCode',
		'RevalidationCodeExpireTime'=> 'users.RevalidationCodeExpireTime',
		'RevalidationDate'			=> 'users.RevalidationDate',
		'IsLocked'					=> 'users.IsLocked',
	);
	protected $_cascadeFields = array();

    public function fetch(AbstractEntity $user)
    {
    	// Check if ID has been set
    	if(isset($user->id)) {
    		$this->findById($user->id, $user);
    	} elseif(isset($user->Username)) {
    		$this->addFilter('Username', $user->Username);
    		$this->findSingleEntity($this->getFilters(), $user);
    	}
    	return $user;
    }

    protected function setEntitySpecificData(AbstractEntity $user)
    {
    	$this->resetFilters();
    	$this->addFilter('id', $user->id);

    	$user->recipes = new CollectionProxy (
    		$this->_dataMapperFactory->build('recipe'),
    		$this->getFilters()
    	);
    }
}