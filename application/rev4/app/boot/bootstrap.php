<?php
namespace Lib\Boot;
use
	Lib\SecureContainer,
	Lib\AccessControlList,
	Lib\Boot\Db\MysqlAdapter,
	Model\ServiceFactory,
	Model\CollectionFactory,
	Model\EntityFactory,
	Model\DataMapperFactory,
	View\ViewFactory,
	Controllers\ControllerFactory;

define('IS_DEVELOPMENT_AREA', true);

/* Confirm that we have initiated the script as intended for security */
define('IN_CONTROLLER', true);

/* Define the paths */
define('ROOT_PATH', '../application/rev4/');
define('SITE_TEMPLATE', 'fluency');
	
/* Configure error reporting */
if(IS_DEVELOPMENT_AREA === true) {
	ini_set('display_errors', 1);
	ini_set('html_errors', '1');
	error_reporting(E_ALL ^ E_NOTICE);
} else {
	error_reporting(0);
}

/* Set up autoloader class */
require ROOT_PATH . 'lib/boot/autoloader.php';
new Autoloader(ROOT_PATH);

/* Creates basic structures, which will be used for interaction with model layer */
$serviceFactory = new ServiceFactory
(
	$dataMapperFactory = new DataMapperFactory
	(	
		new MysqlAdapter(array('localhost', 'user', 'password', 'tableprefix' . WEBSITE)),
		new SessionHandler,
		new CollectionFactory,
		$entityFactory = new EntityFactory
	),
	$entityFactory
);

/* Dispatch */
$request = new Request();

$dispatcher = new Dispatcher
(
	$serviceFactory,
	new ControllerFactory($serviceFactory, $request),
	new ViewFactory($serviceFactory, $request),
	$request
);
//$request->setCommand('login');
$dispatcher->dispatch($request->getResourceName(), $request->getCommand());

/* Do some cleanup exercises before next request */
$serviceFactory
	->build('model')
	->clearModelResponse();
