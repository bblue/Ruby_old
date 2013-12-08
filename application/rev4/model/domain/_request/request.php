<?php
namespace Model\Domain\Request;
use 
	Model\AbstractEntity;

final class Request extends AbstractEntity
{  
    protected $_allowedFields = array('id', 'path_info', 'resourceName', 'method');
    
    public function setId($id)
    {
        $this->_values['id'] = $id;
    }

    public function setPath_info($path_info)
    {
    	$path_info = $this->sanitizePath_info((isset($path_info) ? $path_info : '/'));
    	$this->_values['path_info'] = $path_info;
    }
    
    private function setResourceName()
    {
    	$this->_values['resourceName'] = 'recipes';
    }
    
    private function setMethod()
    {
    	$this->_values['method'] = null;
    }
    
    private function sanitizePath_info($path_info)
    {
		$validation = new Validation();
		$validation->addSource(array('path_info' => $path_info));
		$validation->addValidationRule('path_info', 'url', true);
		$validation->validate();
		return $validation->sanitized['path_info'];
    }
}