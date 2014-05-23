<?php

	/** Check if the installation files are still present */
	if(file_exists('websites/install/index.php')) {
		die('You must complete the installation and delete the install folder before you can continue. Click <a href="websites/install/">here</a> to go to the installation page.');
	}

	$bootfile = '..'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'boot'.DIRECTORY_SEPARATOR.'bootstrap.php';
	if(is_readable($bootfile)) {
		define('WEBSITE', 'self');
		require $bootfile;
	} else {
		throw new \RuntimeException('Unable to load Ruby system boot file (' . $bootfile . ')');
	}
