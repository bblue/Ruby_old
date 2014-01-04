<?php
namespace View\PresentationObjects;
use Model\Domain\Log\Collection;

use View\AbstractPresentationObject;

final class ErrorMessage extends AbstractPresentationObject
{
	public function assignData($iHttpErrorCode, $sHttpErrorHeading, $sHttpErrorText)
	{
		$this->assign_vars(array(
			'CODE'		=> $iHttpErrorCode,
			'HEADING'	=> $sHttpErrorHeading,
			'TEXT'		=> $sHttpErrorText,
		));
	}
}