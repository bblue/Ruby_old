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
		'id'					=> 'visitors.id',
		'timestamp'				=> 'visitors.timestamp',
		'controller'			=> 'visitors.controller',
		'method'				=> 'visitors.method',
		'author_id'				=> 'visitors.user_id',
		'remote_addr'			=> 'visitors.remote_addr',
		'http_user_agent'		=> 'visitors.http_user_agent',
		'http_vars'				=> 'visitors.http_vars',
		'device'				=> 'visitors.device',
		'platform'				=> 'visitors.platform',
		'browser'				=> 'visitors.browser'

	);
	protected $_cascadeFields = array();

    public function fetch(AbstractEntity $recipe)
    {
    	// Check if ID has been set
    	if(isset($recipe->id))
    	{
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