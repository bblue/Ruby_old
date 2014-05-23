<?php
namespace Model\Mappers;

use
	Model\Domain\Recipe\Recipe,
	Model\Domain\Ingredient\Ingredient,
	App\DatabaseDataMapper,
	App\AbstractEntity,
	App\CollectionProxy;

final class RecipeMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'Recipe';

	/*
	protected $_acceptedFields = array(
		'id'			=> 'recipes_old.r_id',
		'title'			=> 'recipes_old.title',
		'author_id'		=> 'recipes_old.author_id',
		'abstract'		=> 'recipes_old.abstract',
		'time_estimate'	=> 'recipes_old.time_estimate',
		'rating'		=> 'recipes_old.rating',
		'initTime'		=> 'recipes_old.initTime',
		'submitTime'	=> 'recipes_old.submitTime',
		'updateTime'	=> 'recipes_old.updateTime',
		'rating'		=> 'recipes_old.rating',
		'status'		=> 'recipes_old.status',
		'method'		=> 'recipes_old.method',
		'portions'		=> 'recipes_old.portions'
	);
	*/
	protected $_acceptedFields = array(
		'id'		=> 'recipes_new.id',
		'title'		=> 'recipes_new.title',
		'method'	=> 'recipes_new.method',
		'abstract'	=> 'recipes_new.abstract',
		'status'	=> 'recipes_new.status',
		'author_id'	=> 'recipes_new.author_id'
	);
	protected $_cascadeFields = array();

    public function fetch(AbstractEntity $recipe)
    {
    	// Check if ID has been set
    	if(isset($recipe->id)) {
    		$this->findById($recipe->id, $recipe);
    		return $recipe;
    	}
    	throw new \Exception('Recipe entity requires ID to be fetched');
    }

    protected function setEntitySpecificData(AbstractEntity $recipe)
    {
    	$this->resetFilters();

    	$this->addFilter('id', $recipe->author_id);
    	$recipe->author = new CollectionProxy (
    		$this->_dataMapperFactory->build('user'),
    		$this->getFilters()
    	);

    	$this->addFilter('r_id', $recipe->id);
    	$recipe->ingredients = new CollectionProxy (
			$this->_dataMapperFactory->build('ingredient'),
    		$this->getFilters()
    	);
    }
}