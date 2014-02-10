<?php
namespace Lib;

final class Systemload
{
    public function getSystemLoad()
    {
        return (function_exists('sys_getloadavg')) ? sys_getloadavg() : $this->sys_getloadavg();
    }
    
    
    private function sys_getloadavg()
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