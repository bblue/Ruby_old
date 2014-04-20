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
		$recipeService = $this->serviceFactory->build('recipe');

		if(isset($this->request->aFormData)) {
			// Build the recipe object
			$recipe = $recipeService->build($this->request->aFormData);

			// Trigger event
			$this->eventHandler->dispatch('controllers.recipes.beforeAdd', $this->eventHandler->buildEvent(array('recipe' => $recipe)));

			$recipeService->add($recipe);

			// Trigger event
			$this->eventHandler->dispatch('controllers.recipes.afterAdd', $this->eventHandler->buildEvent(array('recipe' => $recipe)));

		//} elseif($aParams = $this->serviceFactory->build('session')->getVar('recipes.add')) {
		//	$recipeService->build($aParams);
		} else {
			$aRecipeData = array(
				'AUTHOR_ID'			=> $this->visitor->user_id,
			);
			$recipe = $recipeService->build($aRecipeData);
		}
		return $recipe;
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