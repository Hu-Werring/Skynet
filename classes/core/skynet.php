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
     *$tpl
     *Link to the template engine
     *@access private
     */
    
    private $tpl;
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
        new Core_View();
        //new Lib_RainTPL(); Wont include?? HELP?
        //$this->tpl = new Lib_RainTPL();
        $this->initTemplateEngine();
    }
    
    /**
     * initTemplateEngine
     * Initializes the template engine rainTPL
     * @access private
    */
    private function initTemplateEngine()
    {
        include_once basedir.'classes/lib/raintpl.php';
        //raintpl::$tpl_dir = "view/"; // template directory
        //raintpl::$cache_dir = "tmp/"; // cache directory
        $this->tpl = new RainTPL();
        $this->tpl->configure('tpl_dir', 'view/');
        $this->tpl->configure('cache_dir', 'tmp/');
    }
    /**
     * testing of base classes
    */
    private function testBaseClasses(){
        
        //---------------DEBUG/TESTING------------------//
        if($this->reg->debug->allowDebug)
        {
           // $this->tpl->assign('lol', 'hoi');
            $this->tpl->draw('home');
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