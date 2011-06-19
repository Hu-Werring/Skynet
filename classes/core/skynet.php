<?php

/*
 * class Core_Skynet
 */

class Core_Skynet {
    
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg = null;
    
    /**
     * $view
     * Direct link to view class. (From registry).
     * @access private
     */
    private $view;

    /**
     * $disableMysql
     * Don't start Core_Database
     * @access private
     * 
    */
    private $disableMysql;
    /**
     * __construct
     * creates base for CMS
     * @param Bool $noMysql Don't start Core_Database
    */
    function __construct($mysql=true) {
        $this->disableMysql = !$mysql;
        $this->initBaseClasses();
    }
    
    /**
     * initBaseClasses
     * Initializes most basic classes
     * @access private
    */
    private function initBaseClasses(){
        $this->reg = new Core_Registery();
        new Core_Settings();
        new Debug_Main();
        if($this->disableMysql === false)
            new Core_Database();
        $this->view = new Core_View();
    }
    
    #############ROUTING#################
    /**
     * main
     * initiate main website
    */
    public function main(){
        new Controllers_Main();
    }
    
    /**
     * main
     * initiate Admin Control Panel
    */
    public function acp(){
        //new Controllers_Main();
        $this->view->draw('main');
    }
    
    /**
     * Install
     * runs the installer
    */
    public function install(){
        $installer = new Core_Installer();
        new Controllers_install();
    }
}

?>