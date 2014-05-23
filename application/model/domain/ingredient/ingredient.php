<?php
namespace Model\Domain\Ingredient;
use App\AbstractEntity;
use App\CollectionProxy;

final class Ingredient extends AbstractEntity
{
	protected $_allowedFields = array(
		'id',
		'ingr_name',
		'unit',
		'value',
		'group',
		'static',
		'optional',
		'replaceable',
		'r_id',
		'comment',
		'relevance'
   	);

	public function setValue($value)
	{
		$this->_values['value'] = (int)$value;
	}
}
