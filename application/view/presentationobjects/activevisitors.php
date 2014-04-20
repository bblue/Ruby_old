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

		$iMemberCount = 0;
		$iGuestCount = 0;

		$aRecordedVisitor = array();

		foreach($visitors as $visitor) {
			if(in_array($visitor->user_id, $aRecordedVisitor)) {
				continue;
			}

			if(!$visitor->isLoggedIn()) {
				$iGuestCount++;
				continue;
			}

			$aRecordedVisitor[] = $visitor->user_id;

			$dateTime->setTimestamp($visitor->timestamp);

			$this->assign_block_vars('visitors', array(
				'USER_ID'			=> $visitor->user_id,
				'USERNAME' 			=> $visitor->user->Username,
				'IP' 				=> $visitor->remote_addr,
				'HTTP_USER_AGENT'	=> $visitor->http_user_agent,
				'LAST_SEEN_ONLINE'	=> ucfirst($dateformat->formatDateDiff($dateTime)),
				'DEVICE'			=> $visitor->getDevice(),
				'PLATFORM'			=> $visitor->getPlatform(),
				'BROWSER'			=> $visitor->getBrowser(),
				'FIRSTNAME'			=> $visitor->user->Firstname,
				'LASTNAME'			=> $visitor->user->Lastname
			));

			$iMemberCount++;
		}

		$this->assign_vars(array(
			'VISITORS_MEMBERCOUNT'	=> $iMemberCount,
			'VISITORS_GUESTCOUNT'	=> $iGuestCount
		));
	}
}