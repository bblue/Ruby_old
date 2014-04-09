<?php
namespace App\Factories;
use App\Factory;
use App\Boot\Request;
use App\Boot\Autoloaders\Moduleloader;

final class Controller extends Factory
{
	private $serviceFactory;
	private $request;
	private $eventHandler;

	public function __construct(Service $serviceFactory, Request $request)
	{
		$this->serviceFactory = $serviceFactory;
		$this->request = $request;
	}

	protected function construct($sControllerName)
	{
	    // Settings @todo: Bring these into variables on global level
	    $sNamespace = 'Modules';
	    $sFileExt = '.controller.php';
	    $sClassSuffix = 'Controller';

	    $loader = new Moduleloader($sClassSuffix, $sFileExt);
        $loader->register();

        // Extract elements
        $aPathElements = explode('/', $sControllerName);

        // Make first char uppercase
        $aPathElements = array_map('ucfirst', $aPathElements);

	    // Implode back to string
	    $sControllerName = implode('\\', $aPathElements);

	    // Add suffix and namespace to class name
	    $sClassName = $sNamespace . '\\' . $sControllerName . $sClassSuffix;

	    $class = new $sClassName($this->serviceFactory, $this->request);

	    $loader->unregister();

	    return $class;
	}
}