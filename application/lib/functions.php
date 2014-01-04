<?php
namespace Lib;
class Functions
{
	static function cookie_var($sVarname, $default = NULL)
	{
		return ((isset($_COOKIE[$sVarname]) && !empty($_COOKIE[$sVarname])) ? $_COOKIE[$sVarname] : $default );
	}

	static function get_var($sVarname, $default = NULL)
	{
		return ((isset($_GET[$sVarname]) && !empty($_GET[$sVarname])) ? $_GET[$sVarname] : $default );
	}

	static function post_var($sVarname, $default = NULL)
	{
		return ((isset($_POST[$sVarname]) && !empty($_POST[$sVarname])) ? $_POST[$sVarname] : $default );
	}

	static function request_var($sVarname, $default = NULL)
	{
		return ((isset($_REQUEST[$sVarname]) && !empty($_REQUEST[$sVarname])) ? $_REQUEST[$sVarname] : $default );
	}

	static function session_var($sVarname, $default = NULL)
	{
		return ((isset($_SESSION[$sVarname]) && !empty($_SESSION[$sVarname])) ? $_SESSION[$sVarname] : $default );
	}

	static function hash($sString)
	{
		return md5(md5(SALT) . md5($sString) . md5(PEPPER));
	}
	
	function in_array_r($needle, $haystack, $strict = false)
	{
	    foreach ($haystack as $item)
	    {
	        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict)))
	        {
	            return true;
	        }
	    }
	    return false;
	}
	
	static function get_type($key, $value)
	{
		$type = substr($key, 0, 1);
		switch($type){
			default: throw new exception('Unknown type identifier when reading ' . $type); break;
			case 'b': return ((int)$value === 1) ? true : false; break;
			case 'i': return (int)$value; break;
			case 's': return $value; break;
		}	
	}
	
	static function unix_diff($iStart)
	{
		$date = self::convert_unix_to_datetime($iStart);
		$end = new DateTime();
		$diff = $date->diff($end);
		return $diff->format('%a');
	}
	
	static function convert_unix_to_datetime($unix)
	{
		$dateTime = new DateTime();
		$dateTime->setTimestamp($unix);
		return $dateTime;
	}
	
	static function time_difference($sStart = 'now', $sEnd = 'now')
	{
	    $iEnd = strtotime($sEnd);
	    $iStart = strtotime($sStart);
	    $iDiff = sqrt(($iEnd - $iStart)^2);
	    return round($iDff / 86400);
	}
	
	static function format_timestamp($iTimestamp, $format = 'jS F Y')
	{
		$date = self::convert_unix_to_datetime($iTimestamp);
		return $date->format($format);
	}
	
	static function formatDateDiff($start, $end=null) {
	    if(!($start instanceof DateTime)) {
	    	//$timestamp = $start;
	        $start = new DateTime($start);
	        //$start->setTimestamp($timestamp);
	    }
	   
	    if($end === null) {
	        $end = new DateTime();
	    }
	   
	    if(!($end instanceof DateTime)) {
	    	//$timestamp = (is_int($start)) ? $start : false;
	        $end = new DateTime($start);
	        //if($timestamp) { $start->setTimestamp($end); }
	    }
	   
	    $interval = $end->diff($start);
	    $doPlural = function($nb,$str){ return $nb > 1 ? $str . 's' : $str; }; // adds plurals
	   
	    $format = array();
	    if($interval->y !== 0) {
	        $format[] = "%y ".$doPlural($interval->y, "year");
	    }
	    if($interval->m !== 0) {
	        $format[] = "%m ".$doPlural($interval->m, "month");
	    }
	    if($interval->d !== 0) {
	        $format[] = "%d ".$doPlural($interval->d, "day");
	    }
	    if($interval->h !== 0) {
	        $format[] = "%h ".$doPlural($interval->h, "hour");
	    }
	    if($interval->i !== 0) {
	        $format[] = "%i ".$doPlural($interval->i, "minute");
	    }
	    if($interval->s !== 0) {
	        if(!count($format)) {
	            return 'less than a minute ago';
	        } else {
	            $format[] = "%s ".$doPlural($interval->s, "second");
	        }
	    }
	    if($interval->s === 0 && !count($format)) {
	    	return 'this very moment';
	    }
	   
	    // We use the two biggest parts
	    if(count($format) > 1) {
	        $format = array_shift($format)." and ".array_shift($format);
	    } else {
	        $format = array_pop($format);
	    }
	   
	    return $interval->format($format);
	}	
	
	static function convert_byte_size($size)
	{
		$unit=array('B','KiB','MiB','GiB','TiB','PiB');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}

	static function recursive_unset(&$array, $unwanted_key)
	{
		unset($array[$unwanted_key]);
		foreach ($array as &$value)
		{
			if(is_array($value))
			{
				recursive_unset($value, $unwanted_key);
			}
		}
	}

	static function get_folder_size($dir)
	{
		$bytes = 0;
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
		foreach ($iterator as $i) 
		{
		  $bytes += $i->getSize();
		}
		return $bytes;
	}
	
	static function mkdir_recursive($pathname)
	{
    	is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname));
    	return is_dir($pathname) || @mkdir($pathname);
	}
	
	static function build_array_from_keys($array)
	{
		foreach ($array as $key => $value)
		{
			$aKeyArray[] = $key;
		}
		return $aKeyArray;
	}
	
  static function MakePrettyException(Exception $e) {
    $trace = $e->getTrace();

    $result = 'Exception: "';
    $result .= $e->getMessage();
    $result .= '" @ ';
    if($trace[0]['class'] != '') {
      $result .= $trace[0]['class'];
      $result .= '->';
    }
    $result .= $trace[0]['function'];
    $result .= '();<br />';
    $trace = array_reverse($trace);
    $tab = '&nbsp;';
    foreach($trace as $where){
    	$tabs .= $tab;
    	$result .= $tabs . $where['class'] . '->' . $where['function'] .'<br />';
    }

    return $result;
  }
  
	static function load_specific_file($file)
	{
		if(!is_readable($file))
		{
			throw new Exception('Script is unable to load "<i>' . $file . '</i>"');
		} else {
			include $file;
		}
	}
	static function getServerLoad($windows = false){
	    $os=strtolower(PHP_OS);
	    if(strpos($os, 'win') === false){
	        if(file_exists('/proc/loadavg')){
	            $load = file_get_contents('/proc/loadavg');
	            $load = explode(' ', $load, 1);
	            $load = $load[0];
	        }elseif(function_exists('shell_exec')){
	            $load = explode(' ', `uptime`);
	            $load = $load[count($load)-1];
	        }else{
	            return false;
	        }
	
	        if(function_exists('shell_exec'))
	            $cpu_count = shell_exec('cat /proc/cpuinfo | grep processor | wc -l');        
	
	        return array('load'=>$load, 'procs'=>$cpu_count);
	    }elseif($windows){
	        if(class_exists('COM')){
	            $wmi=new COM('WinMgmts:\\\\.');
	            $cpus=$wmi->InstancesOf('Win32_Processor');
	            $load=0;
	            $cpu_count=0;
	            if(version_compare('4.50.0', PHP_VERSION) == 1){
	                while($cpu = $cpus->Next()){
	                    $load += $cpu->LoadPercentage;
	                    $cpu_count++;
	                }
	            }else{
	                foreach($cpus as $cpu){
	                    $load += $cpu->LoadPercentage;
	                    $cpu_count++;
	                }
	            }
	            return array('load'=>$load, 'procs'=>$cpu_count);
	        }
	        return false;
	    }
	    return false;
	}
}