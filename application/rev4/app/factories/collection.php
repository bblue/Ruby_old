<?php
namespace App\Factories;
use App\Factory;

final class Collection extends Factory
{
	protected function construct($sEntityName)
	{
		$sCollectionName = 'Model\Domain\\' . $sEntityName . '\\Collection';
		return new $sCollectionName();
	}
}