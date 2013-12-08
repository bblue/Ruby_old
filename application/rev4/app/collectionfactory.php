<?php
namespace Model;
use Lib\AbstractFactory;

final class CollectionFactory extends AbstractFactory
{
	protected function construct($sEntityName)
	{
		$sCollectionName = 'Model\Domain\\' . $sEntityName . '\\Collection';
		return new $sCollectionName();
	}
}