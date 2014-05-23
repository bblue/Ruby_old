<?php
namespace View\PresentationObjects;

use Model\Domain\Log\Log as LogEntry;

use View\AbstractPresentationObject;

final class Log extends AbstractPresentationObject
{
	public function assignData(array $aLogs)
	{
		if(empty($aLogs)) {
			return false;
		}

		foreach($aLogs as $logEntry) {
			if($logEntry instanceof LogEntry) {
				if($logEntry->bShowLog) {
					$this->template->assign_block_vars('logs', array(
						'TEXT'		=> $logEntry->text,
						'TYPE'		=> $logEntry->type
					));
				}
			}
		}
		$this->template->set_filenames(array('log' => 'blocks/logEntry.htm'));
		$this->template->assign_display('log', 'LOG', true);
	}
}