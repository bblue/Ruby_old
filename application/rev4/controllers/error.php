<?php
namespace Controllers;

use App\AbstractController;

final class Error extends AbstractController
{	
	public function set403error()
	{
		return true;
	}
	
	public function set404error()
	{
		return true;
	}
	
	public function set500error()
	{
		return true;
	}

	public function setMaintenance()
	{
		return true;
	}
}