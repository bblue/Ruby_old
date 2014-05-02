<?php
namespace Model\Services;

use App\ServiceAbstract;
use Model\Domain\Recipe\Recipe as RecipeEntity;
use model\Domain\User\User as Author;

final class Recipe extends ServiceAbstract
{
	public $recipe; // Current recipe being modified
	public $recipes;
	private $author;
	public $iNumRows;

	public function find(array $aCriterias = array(), $order = '', $page = null, $iItemsToShow = 10)
	{
		$dataMapper = $this->dataMapperFactory->build('recipe');

		$count = $dataMapper->count($aCriterias);
		$offset = ($page) ? $iItemsToShow * $page : null;

		$this->recipes = $dataMapper->find($aCriterias, null, array(), $order, $offset + $iItemsToShow, $offset);
	}

	public function count(array $aCriterias = array())
	{
		$this->iNumRows = $this->dataMapperFactory->build('recipe')->getCount($aCriterias);
	}

	public function getCount()
	{
		if(isset($this->iNumRows)) {
			return $this->iNumRows;
		} else {
			throw new \Exception('Count has not been set');
		}
	}

	public function setAuthor(Author $author)
	{
		$this->author = $author;
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
		if(!$this->hasRequiredData()) {
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

		// Check entity contains all required input
		if(!$this->hasRequiredData()) {
			return $this->recipe;
		}

		// Check for entity errors
		if($this->recipe->hasError()) {
			return $this->recipe;
		}

		// Register in database
		$this->dataMapperFactory
			->build('recipe')
			->update($this->recipe);

		return $this->recipe;
	}

	private function hasRequiredData()
	{
		$aRequiredKeys = array('title', 'author_id', '');

		foreach($aRequiredKeys as $key) {
			if(!isset($this->recipe->$key)) {
				$this->recipe->setError($key . ' is a required field');
				$bHasError= true;
			}
		}
		return $bHasError ? false : true;
	}

	public function build($aParams = array())
	{
		$this->recipe = $this->entityFactory->build('recipe');

		if(!empty($aParams)) {
			foreach($aParams as $key => $value) {
				$this->recipe->$key = $value;
			}
		} else {
			// Check for stored recipe in session
			$this->dataMapperFactory->build('session')->fetch($this->recipe);
		}

		if($this->author) {
			$this->recipe->author_id = $this->author->id;
			$this->recipe->author = $this->author;
		}

		return $this->recipe;
	}
}