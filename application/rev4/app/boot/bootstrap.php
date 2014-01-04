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
		ini_set('error_prepend_string', '<pre>'); 
		ini_set('error_append_string', '</pre>'); 
		if(PRINT_TEMPLATE_VARS === true || PRINT_SQL_QUERY === true || PRINT_CONTROLLER_COMMAND === true)
		{
			echo '<br /><br />'; // Ugly fix to push the <pre> tags below the floating header menu in bootstrap theme
		}
	} else
	{
		error_reporting(0);
	} 
	
} else {
	error_reporting(0);
}

/** Get the current system load */
if(CALCULATE_CPU_LOAD === true)
{	
	if (!function_exists('sys_getloadavg')) {
		
		function sys_getloadavg($windows = false)
		{
		    if(stristr(PHP_OS, 'win')) {
		        if(class_exists('\COM')){
		            $wmi = new \COM('WinMgmts:\\\\.');
		            //$bits = (PHP_INT_SIZE === 2147483647) ? '32' : '64';
		            $cpus = $wmi->InstancesOf('Win32_Processor');
		            $load = 0;
		            $cpu_count = 0;
		            if(version_compare('4.50.0', PHP_VERSION) == 1){
		                while($cpu = $cpus->Next()){
		                    $load += $cpu->LoadPercentage;
		                    $cpu_count++;
		                }
		            } else { 
		                foreach($cpus as $cpu){
		                    $load = $cpu->LoadPercentage;
		                    $cpu_count++;
		                }
		            }
		            return array(($load/$cpu_count), 0, 0);
		        } else {
		            return array(0,0,0);
		        }
		    } else {
		        if(ini_get('safe_mode') == 'On') {
	                return array(0,0,0);
	            }
		        
	            // Suhosin likes to throw a warning if exec is disabled then die - weird
	            if($func_blacklist = ini_get('suhosin.executor.func.blacklist')) {
	                if(strpos(",$func_blacklist,", 'exec') !== false)
	                {
	                    return array(0,0,0);
	                }
	            }
	            
		    	$loadavg_file = '/proc/loadavg';
				if (file_exists($loadavg_file)) {
	            	return explode(chr(32),file_get_contents($loadavg_file));
	        	} elseif(function_exists('shell_exec')) {
				    $str = substr(strrchr(shell_exec('uptime'),':'),1);
				    $avs = array_map('trim',explode(',',$str));
				    return $avs;
		        } else {
		            return array(0,0,0);
		        }
		    }
		    return array(0,0,0);
		}
	}
	$load = sys_getloadavg();
	if ($load[0] > 80) {
	    header('HTTP/1.1 503 Too busy, try again later');
	    die('Server too busy. Please try again later.');
	}
}

/** Set up autoloader class */
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