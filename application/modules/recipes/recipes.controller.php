<?php
namespace Modules;

use App\AbstractController;

final class RecipesController extends AbstractController
{
	public function executeIndexaction()
	{
		return $this->load('managemyrecipes');
	}

	public function executeManagemyrecipes()
	{
		return true;
	}

	public function executeView()
	{
	    return true;
	}

	public function executeAdd()
	{
	    $event = $this->eventHandler->buildEvent(array('user' => $user));
	    $this->eventHandler->dispatch('recipes.add', $event);
		return true;
	}

	public function executeValidate()
	{
		return true;
	}

	protected function executeGetcategories()
	{
		return true;
	}
}