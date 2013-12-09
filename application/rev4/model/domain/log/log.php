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
    	'timestamp'
    );
    
    /** Set entity ID */
    public function setId($id)
    {
        if(!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999)))) {
            throw new \Exception('The specified ID is invalid ('.$id.')');
        }
        $this->_values['id'] = $id;
    }

}