<?php
 	/** Quick and easy, get the page going! Define website and load the controller. */
	if(is_readable($bootfile = '../application/rev4/app/boot/bootstrap.php')) {
		define('WEBSITE', 'self');
		require $bootfile;
	} else {
		throw new \RuntimeException('Unable to load Ruby system boot file (' . $bootfile . ')');
	}