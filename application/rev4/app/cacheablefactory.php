<?php
namespace App;

use App\CacheableFactory,
	App\Factory;

abstract class CacheableFactory extends Factory
{
	public function build($sClassName, $cache = true)
	{
		return parent::build($sClassName, $cache);
	}
}