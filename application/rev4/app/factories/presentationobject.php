<?php
namespace App\Factories;

use App\Factory;


final class PresentationObject extends Factory
{
	protected function construct($sPresentationObject)
	{
		$sPresentationObject = '\\View\\Presentationobjects\\' . $sPresentationObject;
		return ((!class_exists($sPresentationObject)) ? false : new $sPresentationObject());
	}
}