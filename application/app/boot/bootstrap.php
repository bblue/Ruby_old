<?php
namespace App\Boot;

use App\Factories\DataMapper 	as DataMapperFactory,
	App\Factories\Entity 		as EntityFactory,
	App\Factories\Controller 	as ControllerFactory,
	App\Factories\View 			as ViewFactory,
	App\Factories\Collection 	as CollectionFactory,
	App\Factories\Service 		as ServiceFactory,
    App\Factories\Listener      as ListenerFactory;

use Lib\Db\MysqlAdapter;

if (version_compare(PHP_VERSION, '5.3.1', '<')) {
	die('Your host needs to use PHP 5.3.1 or higher to run this version of Ruby!');
}

/** Confirm that we have initiated the script as intended for security */
define('IN_CONTROLLER', true);
+
/** Define the paths */
define('ROOT_PATH', '..'. DIRECTORY_SEPARATOR . 'application');

/** Load the configuration file  */
require (ROOT_PATH . DIRECTORY_SEPARATOR . 'app'. DIRECTORY_SEPARATOR . 'boot'. DIRECTORY_SEPARATOR . 'config.php'); //@todo: Load this into a class

/** Configure error reporting */
if(IS_DEVELOPMENT_AREA === true) {
    if (!isset($_SERVER['HTTP_CLIENT_IP']) && !isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $whitelist = array('127.0.0.1', 'fe80::1', '::1', '192.168.1.20');
        if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            // Show errors
            ini_set('display_errors', 1);
            ini_set('html_errors', 1);
            error_reporting(E_ALL ^ E_NOTICE);

            // Make errors pretty
            ini_set('error_prepend_string', '<pre>');
            ini_set('error_append_string', '</pre>');

            // Create constant for use in rest of script
            define('DEV_AREA_CONFIRMED', true);
        }
    }
}

/** Set up autoloader class */
require ROOT_PATH . DIRECTORY_SEPARATOR .'app'. DIRECTORY_SEPARATOR . 'boot'. DIRECTORY_SEPARATOR . 'autoloaders' . DIRECTORY_SEPARATOR . 'autoloader.php';
new Autoloaders\Autoloader(ROOT_PATH);

/** Get the current system load */
if(CALCULATE_CPU_LOAD === true) {
    $systemLoad = new Systemload();
    $load = $systemLoad->getSystemLoad();
    if ($load[0] > 80) {
        header('HTTP/1.1 503 Too busy, try again later');
        die('Server too busy. Please try again later.');
    }
}

/** Create basic structures, which will be used for interaction with model layer */
$db 		= new MysqlAdapter(array(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB_TABLE));
$session 	= new SessionHandler();
$request	= new Request($_SERVER['REQUEST_URI']);

/** Create factories */
$entityFactory 		= new EntityFactory();
$collectionFactory 	= new CollectionFactory();
$dataMapperFactory 	= new DataMapperFactory($db, $session, $collectionFactory, $entityFactory);
$serviceFactory		= new ServiceFactory($dataMapperFactory, $entityFactory);
$viewFactory		= new ViewFactory($serviceFactory, $request);
$controllerFactory	= new ControllerFactory($serviceFactory, $request);
$listenerFactory    = new ListenerFactory($serviceFactory);

/** Configure disk logger */
//$diskLogger = $this->serviceFactory->build('diskLogger', true);
//$diskLogger->setLogFilePath('/logs/test.log');

/** Set up the dispatch method */
$dispatcher = new Dispatcher();
$dispatcher->setControllerFactory($controllerFactory);
$dispatcher->setViewFactory($viewFactory);

/** Initialize the frontController and get the page running */
$frontController = new FrontController($dispatcher, $serviceFactory, $listenerFactory);
$frontController->run($request);