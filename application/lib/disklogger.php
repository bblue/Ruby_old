<?php
namespace Lib;

final class DiskLogger
{
    private $sLogFilePath = '';
    
    public function __construct($sLogFilePath)
    {
        $this->sLogFilePath = $sLogFilePath;
    }
    
    
}