<?php
namespace View;

use Lib\AbstractFactory;

final class PresentationObjectFactory extends AbstractFactory
{
	protected function construct($sPresentationObject)
	{
		$sPresentationObject = '\\View\\Presentationobjects\\' . $sPresentationObject;
		return ((!class_exists($sPresentationObject)) ? false : new $sPresentationObject());
	}
}