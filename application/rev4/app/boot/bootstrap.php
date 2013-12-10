<?php
namespace App\Boot;

use App\Factories\DataMapper 	as DataMapperFactory,
	App\Factories\Entity 		as EntityFactory,
	App\Factories\Controller 	as ControllerFactory,
	App\Factories\View 			as ViewFactory,
	App\Factories\Collection 	as CollectionFactory,
	App\Factories\Service 		as ServiceFactory;

use Lib\Db\MysqlAdapter;
	
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
	$whitelist = array('127.0.0.1');
	if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
    	ini_set('display_errors', 1);
		ini_set('html_errors', 1);
		error_reporting(E_ALL ^ E_NOTICE);
	}
} else {
	error_reporting(0);
}

/* Set up autoloader class */
require ROOT_PATH . 'app/boot/autoloader.php';
new Autoloader(ROOT_PATH);

/** Create basic structures, which will be used for interaction with model layer */
$db 		= new MysqlAdapter(array(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB_TABLE));
$session 	= new SessionHandler();
$request	= new Request($_SERVER['PHP_SELF']);

/** Create factories */
$entityFactory 		= new EntityFactory();
$collectionFactory 	= new CollectionFactory();
$dataMapperFactory 	= new DataMapperFactory($db, $session, $collectionFactory, $entityFactory);
$serviceFactory		= new ServiceFactory($dataMapperFactory, $entityFactory);
$viewFactory		= new ViewFactory($serviceFactory, $request);
$controllerFactory	= new ControllerFactory($serviceFactory, $request);

/** Dispatch */
$dispatcher = new Dispatcher();
$dispatcher->setServiceFactory($serviceFactory);
$dispatcher->setControllerFactory($controllerFactory);
$dispatcher->setViewFactory($viewFactory);

$frontController = new FrontController($dispatcher, $serviceFactory);
$frontController->run($request);