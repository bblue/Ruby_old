<?php
namespace App;

final class EntityFactory extends AbstractFactory
{
	protected function construct($sEntityName)
	{
		$sEntityName = ucfirst($sEntityName);
		$sEntityName = '\\Model\Domain\\' . $sEntityName . '\\' . $sEntityName;
		return new $sEntityName();
	}
}