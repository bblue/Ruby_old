<?php
namespace View\PresentationObjects;

use View\AbstractPresentationObject;
use Model\Domain\Recipe\Collection as RecipeCollection;

final class Recipes extends AbstractPresentationObject
{
	public function assignData(RecipeCollection $recipes)
	{
		$dateTime = new \Datetime();

		foreach($recipes as $recipe) {
			$dateTime->setTimestamp($recipe->submitTime);
			$this->assign_block_vars('recipes', array(
				'TITLE'					=> $recipe->title,
				'TIMESTAMP'				=> $dateTime->format('d-m-Y H:i'),
				'ID'					=> $recipe->id,
				'RELEVANCE'				=> $recipe->relevance,
				'ABSTRACT_TRUNCATED'	=> (strlen($recipe->abstract) > 150) ? substr($recipe->abstract, 0, 150) . '...' : $recipe->abstract
			));
		}
	}
}