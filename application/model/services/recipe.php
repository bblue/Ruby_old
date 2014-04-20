<?php
namespace Model\Services;

use App\ServiceAbstract;
use Model\Domain\Recipe\Recipe as RecipeEntity;

final class Recipe extends ServiceAbstract
{
	public function build($aParams)
	{
		$recipe = $this->entityFactory->build('recipe');

		$recipe->title 		= isset($aParams['title']) ? $aParams['title'] : null;
		$recipe->author_id 	= isset($aParams['author_id']) ? $aParams['author_id'] : null;

		return $recipe;
	}

	public function add(RecipeEntity $recipe)
	{
		// Save to database
		$recipe->id = 1337;
		return $recipe;
	}
}