<?php
    namespace App\Boot\Autoloaders;

    final class Moduleloader
    {
        private $sClassSuffix;
        private $sFileExt;

		public function __construct($sClassSuffix, $sFileExt)
		{
		    $this->sClassSuffix = $sClassSuffix;
		    $this->sFileExt = $sFileExt;
		}

        private function load($sClassName)
        {
        	$sInput = $sClassName;

    	    // Extract directories from namespace
    	    $aNamespaceElements = explode('\\', $sClassName);

    	    // Extract class name from namespace and shorten the array
    	    $sClassName = array_pop($aNamespaceElements);

    	    // Save class name without suffix
    	    $sClassName = strstr($sClassName, $this->sClassSuffix, true);

    	    // Prepare the directories array
    	    $aDirectories = $aNamespaceElements;
    	    $aDirectories[] = $sClassName;

    	    // Add suffix to filename and make lowercase
    	    $sFileName = strtolower($sClassName) . $this->sFileExt;

    	    $sFilePath = ROOT_PATH . DIRECTORY_SEPARATOR . strtolower(implode(DIRECTORY_SEPARATOR, $aDirectories) . DIRECTORY_SEPARATOR . $sFileName);

    	    if(is_readable($sFilePath)) {
    	      require $sFilePath;
    	    } else {
    	        $this->unregister();
    	    }
        }
        public function register()
        {
            spl_autoload_register(array($this, 'load'));
        }

        public function unregister()
        {
            spl_autoload_unregister(array($this, 'load'));
        }

        public function __destruct()
        {
            $this->unregister();
        }
    }