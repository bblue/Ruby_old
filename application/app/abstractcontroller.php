<?php
namespace App;

use App\Boot\Request;

use App\Factory;
use View\AbstractView as View;

abstract class AbstractController
{
	protected $serviceFactory;
	protected $request;
	protected $eventHandler;
	private $view;
	
	protected $rbac;
	protected $visitor;
	protected $log;

	public function __construct(Factory $serviceFactory, Request $request)
	{
		$this->serviceFactory = $serviceFactory;
		$this->request = $request;
	}
	
	protected function load($sCommand)
	{
		if(PRINT_CONTROLLER_COMMAND === true) {
    	    $classname = strtolower(get_called_class());
    	
    	    if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
    	        $classname = $matches[1];
    	    }	
			echo '<pre>Performing command on controller: <i>$' . $classname . '->' . $sCommand . '();</i></pre>';
		}
		
		$sCommand = ucfirst(strtolower($sCommand));
		
		$mutator = 'execute' . $sCommand;

		if (!method_exists($this, $mutator) || !is_callable(array($this, $mutator))) {
			throw new \Exception($sCommand . ' could not be called on ' . get_called_class());
		}

		// Inform the view of what command we are performing
		$this->view->setCommand($sCommand);

		return $this->$mutator();
	}

	public function execute($sCommand, View $view)
	{
		// Load the RBAC at the last minute
		require ROOT_PATH . 'lib/PhpRbac/autoload.php'; //@todo: Create a service from this
		$this->rbac = new \PhpRbac\Rbac();

		// Load the logging mechanism
		$this->log = $this->serviceFactory->build('logging', true);
		
		// Load the event handler
		$this->eventHandler = $this->serviceFactory->build('eventHandler', true);
		
		// Preload the visitor
		$this->visitor = $this->serviceFactory->build('recognition', true)->getCurrentVisitor();
		
		$this->view = $view;
		
		return $this->load($sCommand);
	}

	protected function executeIndexaction()
	{
		return true;
	}

	protected function executeSet403error()
	{
		return true;
	}

	public function registerCurrentVisitor()
	{
		$recognition = $this->serviceFactory->build('recognition', true);
		$recognition->registerVisitor($recognition->getCurrentVisitor());
	}
}