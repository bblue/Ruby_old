<?php
namespace Modules;

use App\AbstractController;

final class ErrorController extends AbstractController
{
	public function executeSet500error()
	{
		return true;
	}

	public function executeSetmaintenance()
	{
		return true;
	}
}