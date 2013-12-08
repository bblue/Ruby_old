<?php
namespace Model\Mappers;

use Model\DatabaseDataMapper;

final class RecipeMapper extends DatabaseDataMapper
{
	protected $_entityClass = 'Recipe';
    protected $_dbTable = 'recipes_old';
}