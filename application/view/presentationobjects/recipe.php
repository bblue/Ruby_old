<?php
namespace View\PresentationObjects;

use View\AbstractPresentationObject;
use Model\Domain\Recipe\Recipe as RecipeEntity;

final class Recipe extends AbstractPresentationObject
{
	public function assignData(RecipeEntity $recipe)
	{
		foreach($recipe as $key => $value) {
			switch($key) {
				default: $this->assign_var(strtoupper($key), $value); break;
				case 'author':
					$this->assign_vars(array(
						'AUTHOR_LASTNAME'		=> $value->Lastname,
						'AUTHOR_FIRSTNAME'		=> $value->Firstname
					));
					break;
				case 'ingredients':

					break;
			}
		}
	}
}