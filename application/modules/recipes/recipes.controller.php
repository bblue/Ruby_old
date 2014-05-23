<?php
namespace Modules;

use App\AbstractController;

final class RecipesController extends AbstractController
{
	public function executeIndexaction()
	{
		return $this->load('managemyrecipes');
	}

	public function executeSearch()
	{
		$recipeService = $this->serviceFactory->build('recipe', true);
		$search = $this->serviceFactory->build('search', true);

		$id =	$search->addFilter('recipe.status', 3);
		$id =	$search->addFilter('recipe.status', 1, $id, '>=', 'OR');
				$search->addFilter('recipe.author_id', $this->visitor->user_id, $id);

		$search->addFulltextMatch('recipe.method');
		$search->addFulltextMatch('recipe.title');
		$search->addFulltextMatch('recipe.abstract');
		$search->addFulltextMatch('ingredient.ingr_name');
		$search->setFulltextSearch($this->request->_get('search'));

		$aOrderByFields = array('id', 'title');
		$search->setOrderBy(in_array($this->request->_get('order_by'), $aOrderByFields) ? $this->request->_get('order_by') : 'relevance');
		$search->setRequestedPage($this->request->_get('p'));

		$recipeService->search($search);

		return $search;
	}

	public function executeImageUpload()
	{
		$recipeService = $this->serviceFactory->build('recipe', true);

		$recipeService->buildRecipe();
		$recipeService->setAuthor($this->visitor->user);
		$recipeService->manageRecipeImages();
	}

	public function executeManagemyrecipes()
	{
		$recipeService = $this->serviceFactory->build('recipe', true);
		$search = $this->serviceFactory->build('search', true);

		// Set the search/filter parameters
		$search->addFilter('author_id', $this->visitor->user_id);
		$search->addFilter('status', 0, 0, '!=');

		$aOrderByFields = array('id', 'title');
		$search->setOrderBy(in_array($this->request->_get('order_by'), $aOrderByFields) ? $this->request->_get('order_by') : 'id');

		$search->setRequestedPage($this->request->_get('p'));

		$recipeService->find($search);

		return $search;
	}

	public function executeView()
	{
		if(!empty($this->request->aUrlParams[0]) && is_numeric($this->request->aUrlParams[0])) {
			$recipeService = $this->serviceFactory->build('recipe', true);
			$search = $this->serviceFactory->build('search', true);

			$search->addFilter('id', $id = intval($this->request->aUrlParams[0]));
			$recipe = $recipeService->find($search)[$id];

			return $recipe;
		}
	}

	public function executeAdd()
	{
		$recipeService = $this->serviceFactory->build('recipe');
		$recipeService->setVisitor($this->visitor);

		if(isset($this->request->saveRecipe)) {
			$recipeService->buildRecipe($this->request->_post());

			if($recipeService->getRecipe()->author_id != $this->visitor->user_id) {
				// Check for permission to edit author_id
				$recipeService->setAuthor($this->visitor->user);
			}

			$recipeService->add();

		} else {
			$recipeService->buildRecipe();
			$recipeService->setAuthor($this->visitor->user);
		}

		return $recipeService->getRecipe();
	}

	public function executeEdit()
	{
		$recipeService = $this->serviceFactory->buildRecipe('recipe');

		if(isset($this->request->aFormData)) {
			$recipeService->buildRecipe($this->request->aFormData);
			$recipeService->edit();
		}

		return $recipeService->getRecipe();
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