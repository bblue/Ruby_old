<?php
namespace Model\Mappers;

use 
	Model\AbstractDataMapper,
	Model\AbstractEntity,
	Model\Domain\Request\Request;

final class PostMapper extends AbstractDataMapper
{  
	public function fetch(AbstractEntity $entity)
	{
		if(!$entity instanceof Request)
		{
			throw new \Exception(get_class($entity) . ' is not supported by datamapper ' . __CLASS__);
		}
		return $this->fetchRequest($entity);
	}
	
	private function fetchRequest(Request $request)
	{
		foreach($request->getAllowedFields() as $field)
		{
			$data[$field] = $_POST[strtoupper($field)]; 
		}
		$this->setEntityData($request, $data);
	}
}