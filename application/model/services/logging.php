<?php
namespace Model\Services;

use Model\Domain\Visitor\Visitor;

use App\ServiceAbstract;

final class Logging extends ServiceAbstract
{
	public function createLogEntry($text, Visitor $visitor, $type = 'info', $bShowLog = true, $bSaveLog = false)
	{
		$logEntry = $this->entityFactory->build('log');
		
		$logEntry->type 		= $type;
		$logEntry->text 		= $text;
		$logEntry->timestamp 	= time();
		$logEntry->user_id 		= $visitor->user_id;
		$logEntry->bShowLog		= $bShowLog;
		
		if($bSaveLog) {
			$this->dataMapperFactory
				->build('log')
				->insert($logEntry);
		}
		
		$this->_cache[] = $logEntry;

		return true;
	}
	
	public function getCurrentLogs($type = '')
	{
		if($type = '') {
			return $this->_cache;
		} else {
			return $this->_cache;
			//return array_filter($this->_cache, function($logEntry) {
			//	return ($logEntry->type == $type);
			//});	
		}
	}
	
}