<?php
namespace App\Boot;
use
	App\SecureContainer,
	App\AccessControlList,
	Lib\Db\MysqlAdapter,
	App\ServiceFactory,
	App\CollectionFactory,
	App\EntityFactory,
	App\DataMapperFactory,
	App\ViewFactory,
	App\ControllerFactory;

define('IS_DEVELOPMENT_AREA', true);

/* Confirm that we have initiated the script as intended for security */
define('IN_CONTROLLER', true);

/* Define the paths */
define('ROOT_PATH', '../application/rev4/');
define('SITE_TEMPLATE', 'fluency');

/** Load the configuration file  */
include(ROOT_PATH . 'app/boot/config.php');

/* Configure error reporting */
if(IS_DEVELOPMENT_AREA === true) {
	ini_set('display_errors', 1);
	ini_set('html_errors', 1);
	error_reporting(E_ALL ^ E_NOTICE);
} else {
	error_reporting(0);
}

/* Set up autoloader class */
require ROOT_PATH . 'app/boot/autoloader.php';
new Autoloader(ROOT_PATH);

/* Creates basic structures, which will be used for interaction with model layer */
$serviceFactory = new ServiceFactory
(
	$dataMapperFactory = new DataMapperFactory
	(
		new MysqlAdapter(array(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB_TABLE)),
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
