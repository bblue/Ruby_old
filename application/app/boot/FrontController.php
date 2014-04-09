<?php

namespace App\Boot;

use Model\Services\Routing;

use App\Factories\Service as ServiceFactory;
use App\Factories\Listener as ListenerFactory;

final class FrontController
{
	private $serviceFactory;
	private $dispatcher;
	private $listenerFactory;

	public function __construct(Dispatcher $dispatcher, ServiceFactory $serviceFactory, ListenerFactory $listenerFactory)
	{
		$this->dispatcher = $dispatcher;
		$this->serviceFactory = $serviceFactory;
		$this->listenerFactory = $listenerFactory;
	}

	public function run(Request $request)
	{
	    // Get hold of the current visitor and register in visitor database
		$recognition = $this->serviceFactory->build('recognition', true);
		$visitor = $recognition->getCurrentVisitor();
		$recognition->registerVisitor($visitor);

		// Run the requested route via the routing mechanism
		$routing = $this->serviceFactory->build('routing');

		try {
    		$route = $routing->route($request, $visitor);

    		// Create event handler
    		$eventHandler = $this->serviceFactory->build('eventHandler', true);

    		/** Create event listeners */
    		$listener = $this->listenerFactory->build('RecipeLoggerListener');
    		$eventHandler->addListener('recipes.add', array($listener, 'onAddRecipe'));
    		$eventHandler->addListener('recipes.delete', array($listener, 'onDeleteRecipe'));

			// Dispatch to whatever route we ended up with
			$this->dispatcher->dispatch($route);
		} catch (\Exception $e) {
		    $this->serviceFactory
    		    ->build('logging', true)
    		    ->createLogEntry($e->getMessage(), $visitor, 'danger');
			$route = $routing->redirect($routing::ERROR_500_URL);
			$this->dispatcher->dispatch($route);

			if(defined('DEV_AREA_CONFIRMED') && DEV_AREA_CONFIRMED === true && PRINT_EXCEPTIONS_TRACE === true) {
			    echo '<pre>', '<strong>',$e->getMessage(),'</strong><br />';print_r($e->getTraceAsString()); echo'</pre>';
			}
		}
	}
}