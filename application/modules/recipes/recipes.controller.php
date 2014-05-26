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
		// Get an instance of search service
		$search = $this->serviceFactory->build('search', true);

		// Filter the results as provided
		switch($this->request->_url(0)) {
			case 'draft':
				$search->addFilter('recipe.status', 1);
				break;
			case 'private':
				$search->addFilter('recipe.status', 2);
				break;
			case 'published': case null:
				$search->addFilter('recipe.status', 3);
				break;
			case 'all':
				$search->addFilter('recipe.status', 0, 0, '!=');
				break;
			default:
				$this->load('set404error');
				break;
		}
		// Show only the recipes of logged in user
		$search->addFilter('recipe.author_id', $this->visitor->user_id);

		// Order the results
		$aOrderByFields = array('id', 'title');
		$search->setOrderBy(in_array($this->request->_get('order_by'), $aOrderByFields) ? $this->request->_get('order_by') : 'id');

		// Get requested page
		$search->setRequestedPage($this->request->_get('p'));

		// Get an instance of recipe service
		$recipeService = $this->serviceFactory->build('recipe', true);

		if($this->request->_get('search')) {
			$search->addFulltextMatch('recipe.method');
			$search->addFulltextMatch('recipe.title');
			$search->addFulltextMatch('recipe.abstract');
			$search->addFulltextMatch('ingredient.ingr_name');

			$search->setFulltextSearch($this->request->_get('search'));

			$recipeService->search($search);
		} else {
			$recipeService->find($search);
		}

		// Build a new search object
		//$count = $this->serviceFactory->build('search');
		//$count->addFilter('recipe.')

		return $search;
	}

	public function executeView()
	{
		if(!empty($this->request->_url(0)) && is_numeric($this->request->_url(0))) {
			$recipeService = $this->serviceFactory->build('recipe', true);
			$search = $this->serviceFactory->build('search', true);

			$search->addFilter('id', $id = intval($this->request->_url(0)));
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