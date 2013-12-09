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
	protected $_cascadeField = '';
    
    public function fetch(AbstractEntity $user)
    {
    	// Check if ID has been set
    	if(isset($user->id))
    	{
    		return $this->findById($user->id, $user);
    	}

    	// Find by other search options
    	$aCriterias = ($user->Username) ? array('Username' => array(array('operator' => '=', 'value' => $user->Username))) : array();

    	$this->find($aCriterias, $user); //@todo: denne burde akseptere array
    	
    	//@todo: findAll, findbyID og rsten av metodene lager en ny user uansett. Det må kun lages ny user dersom en annen metode enn fetch brukes.
    	//@todo: Det må ikke returneres en collection dersom det kun er ett treff (tror jeg...)
    	
    	//@todo: Registrere visitor burde jeg faktisk gjøre ETTER at action er tatt, slik at jeg slipper styret med login/logout
    	
    }
    
    protected function setEntitySpecificData(AbstractEntity $user)
    {
		$user->recipes = new CollectionProxy($this->_dataMapperFactory->build('recipe'), array('author_id' => array(array('operator' => '=', 'value' => $user->id))));
		$user->usergroups = new CollectionProxy($this->_dataMapperFactory->build('usergroup'), array('u_id' => array(array('operator' => '=', 'value' => $user->id))));
    }
}