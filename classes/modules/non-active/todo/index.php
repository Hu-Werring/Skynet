<?php

/*
 * class Skynet_Module_Todo
 */

class Skynet_Module_Todo implements Modules_Base {
    private $reg;
    
    function __construct(){
        $this->reg = Core_Registery::singleton();
        
        if(!isset($this->reg->plugin_todo)){
            $this->reg->plugin_todo = $this;
        }
    }
    
    
    public function install(){
        //run SQL installer
        
    }
    public function uninstall(){
        //run SQL uninstaller
        
    }
    public function activate(){
        //function triggerd on activation
        if(!$this->reg->debug->validatePHP(__DIR__ . DS . "test.php")){
            return false;
        }
        copy(__DIR__ . DS . "test.php",basedir . "classes" . DS . "manager" . DS . "test.php");
        return true;
    }
    public function deactivate(){
        //function triggerd on deactivation
        if(file_exists(basedir . "classes" . DS . "manager" . DS . "test.php")){
                unlink(basedir . "classes" . DS . "manager" . DS . "test.php");
        }
    }
    public function getOutput(){
        //everything the main website should load into the module bar
        
    }
    public function getACPOutput(){
        //everything the ACP should load in into the module bar
        
    }
    public function getAllowedMethods(){
        //array of all custom public methods in this module
        $public["add"] = array("todo","time");
        $public["update"] = array("todo","time");
        $public["remove"] = array("todo");
    }
    public function set($setting,$value=null){
        
    }
    public function add($todo,$time){
        
    }
    public function update($todo,$time){
        
    }
    public function remove($todo){
        
    }
}

?>