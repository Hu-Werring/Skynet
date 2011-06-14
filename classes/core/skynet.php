<?php

/*
 * class Core_Skynet
 */

class Core_Skynet {
    
    /*
     * __construct()
     */
    
    private $reg;
    
    function __construct() {
        $this->init();
    }
    
    private function init(){
        $this->reg = new Core_Registery();
        new Core_Config();
        new Core_Database();
        
        
        /**
         * Debug lines
        */
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
        /**
         * End of debug lines
        */
    }
}

?>