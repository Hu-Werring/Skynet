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
        new Core_Config();
        new Core_Database();
    }
    
    /**
     * 
    */
    private function testBaseClasses(){
        //test config & registery
        if($this->reg->config->getvar("cms_debug")){
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
        }
    }
}

?>