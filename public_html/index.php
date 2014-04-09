<?php

	/** Check if the installation files are still present */
	if(file_exists('websites/install/index.php')) {
		die('You must complete the installation and delete the install folder before you can continue. Click <a href="websites/install/">here</a> to go to the installation page.');
	}

 	/** Load the root website */
	if(is_readable($filename = 'websites'.DIRECTORY_SEPARATOR.'self'.DIRECTORY_SEPARATOR.'index.php')){
		require $filename;
	} else {
		die('Unable to load root website');
	}
