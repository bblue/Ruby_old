<?php
 	/** Quick and easy, get the page going! Define website and load the controller. */
	if(is_readable($bootfile = '..'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'boot'.DIRECTORY_SEPARATOR.'bootstrap.php')) {
		define('WEBSITE', 'self');
		require $bootfile;
	} else {
		throw new \RuntimeException('Unable to load Ruby system boot file (' . $bootfile . ')');
	}