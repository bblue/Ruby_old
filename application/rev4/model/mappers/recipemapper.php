<?php
namespace Model\Mappers;

use App\DatabaseDataMapper;

final class RecipeMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'Recipe';
    protected $_dbTable = 'recipes_old';
}