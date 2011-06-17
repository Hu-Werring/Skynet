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
        $this->testBaseClasses();
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
    
    
    /**
     * testing of base classes
    */
    private function testBaseClasses(){
        
        //---------------DEBUG/TESTING------------------//
        if($this->reg->debug->allowDebug)
        {
           // $this->tpl->assign('lol', 'hoi');
            $this->view->draw('home');
        }
        
        //test config & registery
        /*
        if($this->reg->settings->settings["system"]["modus"] === "debug"){
            //test database
            var_export($this->reg->database->insert("testje",array("id"=>1)));
            echo PHP_EOL;
            var_export($this->reg->database->insert("testje",array("id"=>2)));
            echo PHP_EOL;
            var_export($this->reg->database->insert("testje",array("id"=>3)));
            echo PHP_EOL;
            print_r($this->reg->database->update("testje",array("id"=>5),array("id"=>3)));
            echo PHP_EOL;
            print_r($this->reg->database->delete("testje",array("id"=>2)));
            echo PHP_EOL;
            print_r($this->reg->database->select("testje","*"));
            echo PHP_EOL;
            var_export($this->reg->database->clearTable("testje"));
        }*/
    }
    /**
     * main
     * initiate main website
    */
    public function main(){
        $this->reg->debug->dump("main");
    }
    /**
     * main
     * initiate Admin Control Paneld
    */
    public function acp(){
        $this->reg->debug->dump("acp");
    }
    
    /**
     * Install
     * runs the installer
    */
    public function install(){
        new Core_Installer();
    }
}

?>