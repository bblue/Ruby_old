<?php
namespace View\PresentationObjects;

use Lib\DateFormat;
use Lib\Mobile_Detect;

use Model\Domain\Visitor\Collection;

use View\AbstractPresentationObject;

final class ActiveVisitors extends AbstractPresentationObject
{
	public function assignData(Collection $visitors)
	{
		$dateformat = new DateFormat();
		$dateTime = new \Datetime();
		
		foreach($visitors as $visitor)
		{
			$dateTime->setTimestamp($visitor->timestamp);
			
			$this->assign_block_vars('visitor', array(
				'ID' 				=> $visitor->id,
				'USERNAME' 			=> $visitor->user->Username,
				'IP' 				=> $visitor->remote_addr,
				'HTTP_USER_AGENT'	=> $visitor->http_user_agent,
				'LAST_SEEN_ONLINE'	=> ucfirst($dateformat->formatDateDiff($dateTime)),
				'DEVICE'			=> $visitor->getDevice(),
				'PLATFORM'			=> $visitor->getPlatform(), 
				'BROWSER'			=> $visitor->getBrowser(),  
			));
		}
	}
}