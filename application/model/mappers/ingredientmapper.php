<?php
namespace Model\Mappers;

use
	App\DatabaseDataMapper,
	App\AbstractEntity,
	App\CollectionProxy;

final class IngredientMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'Ingredient';

	/*
	protected $_acceptedFields = array(
		'id'				=> 'ingredients.id',
		'ingr_name'			=> 'ingredients.ingr_name',
		'unit'				=> 'ingredients.unit',
		'group'				=> 'ingredients.group',
		'value'				=> 'ingredients.value',
		'static'			=> 'ingredients.static',
		'optional'			=> 'ingredients.optional',
		'replaceable'		=> 'ingredients.replaceable',
		'r_id'				=> 'ingredients.r_id',
		'comment'			=> 'ingredients.comment'
	);
	*/
	protected $_acceptedFields = array(
		'id'			=> 'ingredients_new.id',
		'ingr_name'		=> 'ingredients_new.ingr_name',
		'r_id'			=> 'ingredients_new.r_id'
	);
	protected $_cascadeFields = array(

	);

    public function fetch(AbstractEntity $entity)
    {
    	// Check if ID has been set
    	if(isset($entity->id)) {
    		$this->findById($entity->id, $entity);
    		return $entity;
    	}
    	throw new \Exception('Recipe entity requires ID to be fetched');
    }

    protected function setEntitySpecificData(AbstractEntity $entity)
    {

    }
}