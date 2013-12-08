<?php
namespace Model\Domain\Usergroup;
use Model\AbstractEntity;

final class Usergroup extends AbstractEntity
{  
    protected $_allowedFields = array(
	    'id', 
	    'sUsergroupname',
    	'active',
    	'description',
    	'g_id',
    	'u_id'
    );
   
    //@todo: fikse at det kun er allowed fields som blir lastet fra databasen (dette skaper problemer siden 'comments' vil være et av de godkjente feltene her)

    public $ADMIN_ID = 1;
    
    public function setId($id)
    {
        if(!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999)))) {
            throw new \Exception('The specified ID is invalid.');
        }
        $this->_values['id'] = $id;
    }
   
    public function setName($sName)
    {
        if (strlen($sName) < 2) {
            throw new \Exception('The specified name is invalid.');
        }
        $this->_values['name'] = $sName;
    }
    
    public function setActive($bActive)
    {
    	$this->_values['Active'] = $bActive;
    }
    
    public function setDescription($sDescription)
    {
    	$this->_values['Description'] = $sDescription;
    }
}