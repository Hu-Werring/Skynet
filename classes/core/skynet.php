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
     *$view
     *Direct link to view class. (From registry).
     *@access private
     */
    private $view;

    /**
     * __construct
     * creates base for CMS
    */
    function __construct() {
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
        $this->view->assign('content', "acp");
        $this->view->draw('main');
    }
    
    /**
     * Install
     * runs the installer
    */
    public function install(){
        $installer = new Core_Installer();
        $this->view->assign('content', $installer->output);
        $this->view->draw('main');
    }
}

?>