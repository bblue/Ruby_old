<?php
namespace App\Factories;

use App\CacheableFactory;


final class PresentationObject extends CacheableFactory
{
	protected function construct($sPresentationObject)
	{
		$sPresentationObject = '\\View\\Presentationobjects\\' . $sPresentationObject;
		return ((!class_exists($sPresentationObject)) ? false : new $sPresentationObject());
	}
}