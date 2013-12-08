<?php
namespace Model\Domain\Model;
use Model\EntityCollection;

use Model\AbstractEntity;

final class Model extends AbstractEntity
{  
    protected $_allowedFields = array(
    	'responseLogIds',
    );
    
    public function hasError()
    {
    	return (sizeof($this->_values['responseLogIds']['error']) > 0) ? $this->_values['reponseLogIds']['error'] : false;
    }

    public function addModelResponseLogId($id, $state = 'default')
    {
    	$this->_values['responseLogIds'][$state][] = $id;
    }
    
    public function clearResponseLogIds()
    {
    	$this->_values['responseLogIds'] = array();
    }
    
    public function getModelResponseLogs($state)
    {
		return $this->_values['responseLogIds'][$state];
    }
    
    
}