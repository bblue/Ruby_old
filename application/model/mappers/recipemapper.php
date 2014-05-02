<?php
namespace Model\Mappers;

use
	Model\Domain\Recipe\Recipe,
	App\DatabaseDataMapper,
	App\AbstractEntity,
	App\CollectionProxy;

final class RecipeMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'Recipe';

	protected $_acceptedFields = array(
		'id'		=> 'recipes_old.r_id',
		'title'		=> 'recipes_old.title',
		'author_id'	=> 'recipes_old.author_id',
		'timestamp'	=> 'recipes_old.updateTime'
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
    	$recipe->author = new CollectionProxy(
    		$this->_dataMapperFactory->build('user'),
    		null,
    		array('id' => array(array('operator' => '=', 'value' => $recipe->author_id)))
    	);
    }
}