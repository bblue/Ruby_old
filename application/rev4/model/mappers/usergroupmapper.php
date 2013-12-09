<?php
namespace Model\Mappers;

use App\DatabaseDataMapper;

final class UsergroupMapper extends DatabaseDataMapper
{
	protected $_acceptedFields = array(
		'id'			=> 'usergroups.id',
		'u_id'		=> 'cascade_usergroups_users.u_id',
	    'sUsergroupname'			=> 'usergroups.sUsergroupname',
    	'active'		=> 'usergroups.active',
    	'description'	=> 'usergroups.description',
	);
	protected $_cascadeField = 'usergroups.id = cascade_usergroups_users.g_id';
	
	protected $_entityClass = 'Usergroup';
}