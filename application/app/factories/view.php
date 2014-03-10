<?php
namespace App\Factories;
use App\Factory;
use App\Boot\Request;
use App\Boot\Autoloaders\Moduleloader;

final class View extends Factory
{
	private $serviceFactory;
	private $request;
	
	public function __construct(Factory $serviceFactory, Request $request)
	{
		$this->serviceFactory = $serviceFactory;
		$this->request = $request;
		
	}
	
	protected function construct($sViewName)
	{
	    // Settings @todo: Bring these into variables on global level
	    $sNamespace = 'Modules';
	    $sFileExt = '.view.php';
	    $sClassSuffix = 'View';
	    
	    $loader = new Moduleloader($sClassSuffix, $sFileExt);
        $loader->register();
	    
        // Extract elements
        $aPathElements = explode('/', $sViewName);
	    
        // Make first char uppercase
        $aPathElements = array_map('ucfirst', $aPathElements);
        
	    // Implode back to string
	    $sViewName = implode('\\', $aPathElements);
	    
	    // Add suffix and namespace to class name
	    $sClassName = $sNamespace . '\\' . $sViewName . $sClassSuffix;
	    
	    $class = new $sClassName($this->serviceFactory, $this->request);
	    
	    $loader->unregister();

	    return $class;
	}
}