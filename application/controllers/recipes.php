<?php
namespace Controllers;

use App\AbstractController;

final class Recipes extends AbstractController
{
	public function executeIndexaction()
	{
		return $this->load('managemyrecipes');
	}
	
	public function executeManagemyrecipes()
	{
		return true;
	}
}