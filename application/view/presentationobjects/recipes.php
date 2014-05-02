<?php
namespace View\PresentationObjects;

use View\AbstractPresentationObject;
use Model\Domain\Recipe\Collection as RecipeCollection;

final class Recipes extends AbstractPresentationObject
{
	public function assignData(RecipeCollection $recipes, $i)
	{
		$dateTime = new \Datetime();

		foreach($recipes as $recipe) {
			$dateTime->setTimestamp($recipe->timestamp);
			$this->assign_block_vars('recipes', array(
				'TITLE'			=> $recipe->title,
				'TIMESTAMP'		=> $dateTime->format('d-m-Y H:i'),
				'ID'			=> $recipe->id
			));
		}
		$this->assign_vars(array(
			'RECIPES_COUNT_SHOWN'		=> $recipes->count(),
			'RECIPES_COUNT_TOTAL'		=> $i
		));
	}
}