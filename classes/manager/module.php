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
    public static $name = "Module Manager";
    public static $desc = "install, uninstall, activate and deactivate your modules";
    
    public $dir;
    
    private $protectedManagers = array();
    
    function __construct() {
        $this->init("module");
        $this->dir = basedir.'classes'.DS.'modules'.DS;
        $this->protectedManagers = array("module","article","base","page","user","group","catagory");
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

            if(file_exists($this->dir.'non-active'.DS.$dirname . DS . "index.php")){
                require_once $this->dir.'non-active'.DS.$dirname . DS . "index.php";
                $modClassName = "Skynet_Module_" . ucwords($dirname);
                $modClass = new $modClassName();
                
                if($modClass->activate()){
                    rename($this->dir.'non-active'.DS.$dirname, $this->dir.'active'.DS.$dirname);
                } else {
                    $this->reg->view->assign("error",$dirname . " was not activated due to errors while trying to activate it.");
                }
            }
        }
    }
    
    public function deactivateModule($dirname)
    {
        if(is_dir($this->dir.'active'.DS.$dirname))
        {
            if(file_exists($this->dir.'active'.DS.$dirname . DS . "index.php")){
                require_once $this->dir.'active'.DS.$dirname . DS . "index.php";
                $modClassName = "Skynet_Module_" . ucwords($dirname);
                $modClass = new $modClassName();
                
                $modClass->deactivate();
                
            }
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
    
    public function installManager($path,$file){
        if(
            !$this->reg->debug->validatePHP($path . $file . ".php")
        ||
            !file_exists($path .$file.".php")
        ||
            array_search($file,$this->protectedManagers)!==false
        )
        {
            return false;
        }
        copy($path .$file . ".php",basedir . "classes" . DS . "manager" . DS . $file . ".php");
        return true;
    }
    
    public function uninstallManager($name){
        
        if(file_exists(basedir . "classes" . DS . "manager" . DS . $name . ".php")){
                unlink(basedir . "classes" . DS . "manager" . DS . $name . ".php");
        }

        return true;
    }
    
}
?>