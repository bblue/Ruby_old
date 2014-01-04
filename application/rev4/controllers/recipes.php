<?php
namespace Controllers;

use App\AbstractController;

final class Recipes extends AbstractController
{
	public function indexAction()
	{
		return $this->managemyrecipes();
	}
	
	public function managemyrecipes()
	{
		return true;
	}
}