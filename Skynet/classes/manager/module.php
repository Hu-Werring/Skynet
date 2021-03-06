<?php

/**
 * user.php
 * @package Skynet_managers
*/
/**
 * class Manager_User
 * Base class for all managers
 * @version 0.1
 * @author Lucas Weijers <lucas.weijers@student.hu.nl>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_managers
 */

class Manager_Module extends Manager_Base {
    
    public $dir;
    
    function __construct() {
        $this->init("module");
        $this->dir = basedir.'classes'.DS.'modules'.DS;
    }

    public function getModules($moduleDir)
    {
        $modules = false;
        $modDir = $this->dir.$moduleDir;
        // Open a known directory, and proceed to read its contents
        if ($handle = opendir($modDir))
        {
            while (($file = readdir($handle)) !== false)
            {
                if ($file != "." && $file != ".." && is_dir($modDir.DS.$file))
                {
                    $jsonFile = $modDir.DS.$file.DS."config.json";
                    if(file_exists($jsonFile))
                    {
                        $module = json_decode(file_get_contents($jsonFile), true);
                        $module['filelink'] = $file;
                        $modules[] = $module;
                    }    
                }
            }
            closedir($handle);
        }
        
        return $modules;
    }
    
    public function readSettings($file)
    {
        
    }
    
    public function checkInstall()
    {
        return $this->getModules('install');
    }
    
    public function installModule($dirname)
    {
        if(is_dir($this->dir.'install'.DS.$dirname))
        {
            rename($this->dir.'install'.DS.$dirname, $this->dir.'non-active'.DS.$dirname);
        }
    }
    
    public function activateModule($dirname)
    {
        if(is_dir($this->dir.'non-active'.DS.$dirname))
        {
            rename($this->dir.'non-active'.DS.$dirname, $this->dir.'active'.DS.$dirname);
        }
    }
    
    public function deactivateModule($dirname)
    {
        if(is_dir($this->dir.'active'.DS.$dirname))
        {
            rename($this->dir.'active'.DS.$dirname, $this->dir.'non-active'.DS.$dirname);
        }
    }
    
    public function uninstallModule($dirname)
    {
        if(is_dir($this->dir.'non-active'.DS.$dirname))
        {
            rename($this->dir.'non-active'.DS.$dirname, $this->dir.'install'.DS.$dirname);
        }
    }
    
}
?>