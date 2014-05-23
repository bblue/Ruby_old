<?php
namespace View\PresentationObjects;

use View\AbstractPresentationObject;
use Model\Domain\Recipe\Recipe as RecipeEntity;

final class Recipe extends AbstractPresentationObject
{
	public function assignData(RecipeEntity $recipe)
	{
		$dateTime = new \Datetime();
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
					// Confirm ingredient data is there
					if(empty($value)) {
						continue;
					}

					// Group array by ingredient groups
					$aSortedIngredients = array();
					foreach($value as $key => $ingredient) {
						$aSortedIngredients[(!empty($ingredient->group) ? $ingredient->group : 0)][] = $ingredient;
					}
					if(array_key_exists(0, $aSortedIngredients)) {
						$aSortedIngredients = array(0 => $aSortedIngredients[0]) + $aSortedIngredients;
					}

					// Assign ingredient data
					foreach($aSortedIngredients as $sGroupName => $aMembers) {
						$this->assign_block_vars('ingredients', array(
							'GROUP_NAME'	=> ($sGroupName === 0) ? false : $sGroupName,
						));
						foreach($aMembers as $ingredient) {
							$this->assign_block_vars('ingredients.group', array( //@todo: gjøre denne dynamisk via $ingr->toArray()
								'INGR_NAME' 	=> $ingredient->ingr_name,
								'STATIC'	 	=> $ingredient->static,
								'REPLACEABLE'	=> $ingredient->replaceable,
								'OPTIONAL' 		=> $ingredient->optional,
								'VALUE' 		=> $ingredient->value,
								'UNIT'			=> $ingredient->unit,
								'ID'			=> $ingredient->id,
								'COMMENT'		=> $ingredient->comment
							));
						}
					}
					break;
				case 'submitTime': case 'initTime': case 'updateTime':
					if($value) {
						$dateTime->setTimestamp($recipe->$key);
						$this->assign_var(strtoupper($key), $dateTime->format('jS F Y'));
					}
					break;
			}
		}
	}
}