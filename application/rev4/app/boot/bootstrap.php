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

if (version_compare(PHP_VERSION, '5.3.1', '<'))
{
	die('Your host needs to use PHP 5.3.1 or higher to run this version of Ruby!');
}
	
/** Confirm that we have initiated the script as intended for security */
define('IN_CONTROLLER', true);

/** Define the paths */
define('ROOT_PATH', '../application/rev4/');

/** Load the configuration file  */
require (ROOT_PATH . 'app/boot/config.php'); //@todo: Load this into a class 

/** Configure error reporting */
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
$serviceFactory = new \App\Factories\Service
(
	$dataMapperFactory = new \App\Factories\DataMapper
	(
		new MysqlAdapter(array(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB_TABLE)),
		new SessionHandler,
		new \App\Factories\Collection,
		$entityFactory = new \App\Factories\Entity
	),
	$entityFactory
);

/* Dispatch */
$request = new Request();

$dispatcher = new Dispatcher
(
	$serviceFactory,
	new \App\Factories\Controller($serviceFactory, $request),
	new \App\Factories\View($serviceFactory, $request),
	$request
);
//$request->setCommand('login');
$dispatcher->dispatch($request->getResourceName(), $request->getCommand());

/* Do some cleanup exercises before next request */
$serviceFactory
	->build('model')
	->clearModelResponse();
