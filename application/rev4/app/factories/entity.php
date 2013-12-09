<?php
namespace App\Factories;
use App\Factory;

final class Entity extends Factory
{
	protected function construct($sEntityName)
	{
		$sEntityName = ucfirst($sEntityName);
		$sEntityName = '\\Model\Domain\\' . $sEntityName . '\\' . $sEntityName;
		return new $sEntityName();
	}
}