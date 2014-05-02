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
		$recipeService = $this->serviceFactory->build('recipe', true);

		// Check for search parameters
		$aCriterias = array(
		/**	'title'						=> $this->request->_get('title'), /** recipe title */
		);

		// Only get recipes for this user
		$aCriterias['author_id'][] = array(
			'value'		=> $this->visitor->user_id,
			'operator'	=> '='
		);

		// Request RecipeService to fetch a collection
		$recipeService->find($aCriterias, $this->request->_get('order'), $this->request->_get('show'), $this->request->_get('offset'));

		// Return the collection
		return $recipeService->collection;
	}

	public function executeView()
	{
		$recipeService = $this->serviceFactory->build('recipe');

		if(!empty($this->request->aUrlParams[0]) && is_numeric($this->request->aUrlParams[0])) {
			$aCriterias['id'][] = array(
				'value'		=> intval($this->request->aUrlParams[0]),
				'operator'	=> '='
			);
			$recipeService->find($aCriterias);
		} else {
			$recipeService->build();
		}
		return $recipeService->recipe;
	}

	public function executeAdd()
	{
		$recipeService = $this->serviceFactory->build('recipe');
		$recipeService->setAuthor($this->visitor->user);

		if(isset($this->request->aFormData)) {
			$recipeService->build($this->request->aFormData);
			$recipeService->add();
		} else {
			$recipeService->build();
		}

		return $recipeService->recipe;
	}

	public function executeEdit()
	{
		$recipeService = $this->serviceFactory->build('recipe');

		if(isset($this->request->aFormData)) {
			$recipeService->build($this->request->aFormData);
			$recipeService->edit();
		} else {
			$recipeService->build();
		}

		return $recipeService->recipe;
	}

	public function executeCheckTitleIsAvailable()
	{
		return true;
	}

	protected function executeGetcategories()
	{
		return true;
	}
}