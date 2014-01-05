<?php
namespace Controllers;

use App\AbstractController;

final class Error extends AbstractController
{
	public function executeSet404error()
	{
		return true;
	}
	
	public function executeSet500error()
	{
		return true;
	}

	public function executeSetmaintenance()
	{
		return true;
	}
}