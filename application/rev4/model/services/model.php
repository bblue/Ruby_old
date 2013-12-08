<?php
namespace Model\Services;

use
	Model\ServiceAbstract
;

final class Model extends ServiceAbstract
{

	public function getModelResponse($state)
	{
		$model = $this->entityFactory->build('model');
		$this->dataMapperFactory
			->build('session')
			->fetch($model);

		if(is_array($responseLogs = $model->getModelResponseLogs($state)))
		{
			foreach($responseLogs as $LogId)
			{
		        $aCriterias['id'][] = array(
		        	'operator'	=> '=',
		        	'value'		=> $LogId
		        );
			}
			return $this->dataMapperFactory
				->build('log')
				->find($aCriterias);	
		}
	}
	
	public function clearModelResponse()
	{
		$model = $this->entityFactory->build('model');
		
		$mapper = $this->dataMapperFactory->build('session');
		$mapper->fetch($model);
			
		$model->clearResponseLogIds();
		
		return $mapper->store($model);
	}
}