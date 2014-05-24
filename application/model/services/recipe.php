<?php
namespace Model\Services;

use App\ServiceAbstract;
use Model\Domain\Recipe\Recipe as RecipeEntity;
use model\Domain\User\User as Author;

final class Recipe extends ServiceAbstract
{
	private $recipe; // Current recipe being modified
	private $recipes = array();
	private $visitor;

	public function manageRecipeImages($recipe = null)
	{
		$recipe = ($recipe) ? : $this->recipe;
		$folder = (isset($recipe->id)) ? $recipe->id : 'temp/' . $recipe->author_id;
		$options = array(
			'upload_url' 		=> '/websites/'.WEBSITE.'/uploads/recipes/'.$folder . '/',
			'upload_dir' 		=> '../public_html/websites/'.WEBSITE.'/uploads/recipes/'.$folder . '/',
			'script_url' 		=> 'imageupload',
			'accept_file_types' => '/\.(gif|jpe?g|png)$/i',
			'max_file_size'		=> 10*1024*1024,
			'user_dirs'			=> (isset($recipe->id)) ? false: true
		);
		require(ROOT_PATH . '/lib/jQueryUpload/UploadHandler.php');
		$upload_handler = new \UploadHandler($options);
	}

	public function find($search)
	{
		$mapper = $this->dataMapperFactory->build('recipe');
		$recipes = $mapper->find($search->getFilters());

		$search->setResult($recipes);

		return $this->recipes = $recipes;
	}

	public function search($search)
	{
		// Search the recipe list
		if($aMatchFields = $search->getFulltextMatches('recipe')) {
				$recipeMapper = $this->dataMapperFactory->build('recipe');
				$recipes = $recipeMapper->match($aMatchFields, $search->getFulltextSearch(), $search->getFilters('recipe'), $search->getOrder(), $search->getLimit(), $search->getOffset());
		}

		// Search the ingredient list
		if($aMatchFields = $search->getFulltextMatches('ingredient')) {
			$mapper = $this->dataMapperFactory->build('ingredient');
			$ingredients = $mapper->match($aMatchFields, $search->getFulltextSearch(), $search->getFilters('ingredient'));

			foreach($ingredients as $ingredient) {
				if(isset($recipes[$ingredient->r_id])) {
					$recipes[$ingredient->r_id]->relevance += $ingredient->relevance;
				} else {
					if($recipe = $recipeMapper->findById($ingredient->r_id)){
						$recipe->relevance = $ingredient->relevance;
						$recipes->add($recipe->id, $recipe);
					}
				}
			}
		}

		$search->setResult($recipes);

		return $this->recipes = $recipes;;
	}

	public function getRecipe()
	{
		return $this->recipe;
	}

	public function getRecipes()
	{
		return $this->recipes;
	}

	public function count(array $aCriterias = array())
	{
		return $this->dataMapperFactory->build('recipe')->getCount($aCriterias);
	}

	public function checkTitleIsAvailable($sTitle)
	{
		$recipeCollection = $this->dataMapperFactory->build('recipe')->find(array('title' => array(array('operator' => '=', 'value' => $sTitle))));
		return sizeof($recipeCollection) == 0;
	}

	public function edit() {
		if(!$this->recipe->id) {
			throw new \Exception('Recipe does not exist');
		}

		// Check entity contains all required input
		if(!$this->recipeHasRequiredData()) {
			return $this->recipe;
		}

		if($this->recipe->hasError()) {
			return $this->recipe;
		}

		return $this->dataMapperFactory
			->build('recipe')
			->update($this->recipe);
	}

	public function add() {
		if($this->recipe->id) {
			throw new \Exception('Recipe is already registered');
		}

		$this->recipe->submitTime = time();

		// Check entity contains all required input
		$this->recipeHasRequiredData();

		// Check for entity errors
		if($this->recipe->hasError()) {
			return $this->recipe;
		}

		// confirm title is available

		// Register in database
		$this->dataMapperFactory
			->build('recipe')
			->insert($this->recipe);

		return $this->recipe;
	}

	private function recipeHasRequiredData()
	{
		$aRequiredKeys = array('title', 'author_id', 'abstract', 'submitTime', 'status');

		$bHasError = false;

		foreach($aRequiredKeys as $key) {
			if(!isset($this->recipe->$key)) {
				$this->recipe->setError($key . ' is a required field');
				$bHasError= true;
			}
		}
		return $bHasError;
	}

	public function setAuthor($author, $recipe = null) {
		$recipe = ($recipe) ? : $this->recipe;

		$recipe->author_id = $author->id;
		$recipe->author = $author;
	}

	public function setVisitor($visitor)
	{
		$this->visitor = $visitor;
	}

	public function buildRecipe($aParams = array())
	{
		$this->recipe = $this->entityFactory->build('recipe');

		// Set recipe data
		$aAllowedFields = $this->recipe->getAllowedFields();
		foreach($aParams as $key => $value) {
			if(in_array($key, $aAllowedFields)) {
				$this->recipe->$key = $value;
			}
		}

		// Set ingredient data
		if(array_key_exists('ingr', $aParams) && is_array($aParams['ingr'])) {
			$mapper = $this->dataMapperFactory->build('ingredient');
			$ingredients = $mapper->buildCollection();
			foreach($aParams['ingr'] as $aIngrData) {
				$ingredient = $this->entityFactory->build('ingredient');
				$aAllowedFields = $ingredient->getAllowedFields();
				foreach($aIngrData as $key => $value) {
					if(in_array($key, $aAllowedFields)) {
						$ingredient->$key = $value;
					}
				}
				$ingredients->add(null, $ingredient);

			}
			$this->recipe->ingredients = $ingredients;
		}

		return $this->recipe;
	}

}