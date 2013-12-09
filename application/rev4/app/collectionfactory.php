<?php
namespace App;

final class CollectionFactory extends AbstractFactory
{
	protected function construct($sEntityName)
	{
		$sCollectionName = 'Model\Domain\\' . $sEntityName . '\\Collection';
		return new $sCollectionName();
	}
}