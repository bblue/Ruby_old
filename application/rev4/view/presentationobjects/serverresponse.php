<?php
namespace View\PresentationObjects;
use Model\Domain\Log\Collection;

use View\AbstractPresentationObject;

final class ServerResponse extends AbstractPresentationObject
{
	public function assignData($logEntryCollection)
	{
		if($logEntryCollection instanceof Collection)
		{
			foreach($logEntryCollection as $logEntry)
			{
				$this->assign_block_vars('response', array(
					'TEXT' 				=> $logEntry->text,
				));
			}
		}
	}
}