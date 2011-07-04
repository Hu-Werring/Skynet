<?php
/**
 * skynet.php
 * @package Skynet_core
*/
/**
 * class Core_Skynet
 * The Skynet core
 * @version 0.2
 * @author Thom Werring <info@werringweb.nl>
 * @author Lucas Weijers <meel_to_lucas@hotmail.com>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_core
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
     * @access public
    */
    public function main(){
        $this->view->setTemplatePath('view/');
        $pages = new Manager_Page();
        new Controllers_Main();
        

    }
    
    /**
     * main
     * initiate Admin Control Panel
     * @access public
    */
    public function acp(){
        //add acp dir to templatepath
        $this->view->setTemplatePath('view/acp/');
        new Controllers_Acp();
    }
    
    /**
     * Install
     * runs the installer
     * @access public
    */
    public function install(){
        $installer = new Core_Installer();
        new Controllers_install();
    }
}

?>