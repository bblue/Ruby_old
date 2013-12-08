<?php
namespace View\PresentationObjects;
use Model\Domain\Log\Collection;

use View\AbstractPresentationObject;

final class Errors extends AbstractPresentationObject
{
	public function assignData($logEntryCollection = array())
	{
		if($logEntryCollection instanceof Collection)
		{
			foreach($logEntryCollection as $logEntry)
			{
				$this->assign_block_vars('error', array(
					'TEXT' 				=> $logEntry->text,
				));
			}
		}
		
	}
}