<?php
    namespace Lib\Boot;

    final class Autoloader
    {
		private $sBasePath;

		public function __construct($path = '')
		{
			$this->register();
			$this->setBasePath($path);
		}
		
        public function setBasePath($sBasePath)
        {
        	$this->sBasePath = $sBasePath;
        }
        
        private function register()
        {
            spl_autoload_register( array($this, 'load'));
        }

        private function load($sClassName)
        {
        	$sFilePath = strtolower($this->sBasePath .  str_replace('\\', '/', $sClassName)) . '.php';
        	
			if(!$this->hasLoadedClass($sFilePath))
			{
				throw new \Exception('Autoloader failed to load ' . $sClassName . ' (' . $sFilePath . ')');
			}
			return true;
        }
        
        private function hasLoadedClass($sFilePath)
        {
            if(is_readable($sFilePath))
			{
				require($sFilePath);
				return true;
			}
			return false;
        }
    }