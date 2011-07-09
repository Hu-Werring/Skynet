<?php

interface Modules_Base {
    
    /**
     * installer functions
     */
    public function install();
    public function uninstall();
    public function activate();
    public function deactivate();
    
    /**
     * Output
    */
    public function getOutput();
    public function getACPOutput();
    public function getAllowedMethods();
    
    /**
     * Input
    */
    public function set($setting,$value=null);

    /**
     * Init
    */
    public function __construct();
}   

?>