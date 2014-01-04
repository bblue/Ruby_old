<?php
namespace Model\Domain\Log;
use App\AbstractEntity;

final class Log extends AbstractEntity
{  
    protected $_allowedFields = array(
    	'id', 
    	'text', 
    	'user',
    	'user_id',
    	'timestamp',
    	'type',
    	'bShowLog'
    );
    
    private $aTypes = array(
	    'info'			=> 'log for information',
	    'warning'		=> 'error log',
    	'danger'		=> 'critical error log',
    	'success'		=> 'success return'
    );
    
    /** Set entity ID */
    public function setId($id)
    {
        if(!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999)))) {
            throw new \Exception('The specified ID is invalid ('.$id.')');
        }
        $this->_values['id'] = $id;
    }

    public function setType($type)
    {
    	if(!$this->isValidType($type))
    	{
    		throw new \Exception('Unknown log type');
    	}
    	
    	$this->_values['type'] = $type;
    }
    
    public function isValidType($type)
    {
        return isset($this->aTypes[$type]);   	
    }
}