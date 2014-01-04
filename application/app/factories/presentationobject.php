<?php
namespace App\Factories;

use View\Template;

use App\Factory;

final class PresentationObject extends Factory
{
	private $template;
	
	public function __construct(Template $template)
	{
		$this->template= $template;
	}
	
	protected function construct($sPresentationObject)
	{
		$sPresentationObject = '\\View\\Presentationobjects\\' . $sPresentationObject;
		return ((!class_exists($sPresentationObject)) ? false : new $sPresentationObject($this->template));
	}
}