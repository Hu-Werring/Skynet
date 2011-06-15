<?php

/*
 * class Debug_Main
 */

class Debug_Main  {
    
    private $reg;
    
    
    function __construct() {
        $this->reg = Core_Registery::singleton();
        $this->reg->debug = $this;
    }
    
    //Deze functie beter???
    /*
    public function __get($name){
        if(isset($this->storage[$name])){
            return $this->storage[$name];
        } else {
            return null;
        }
    }*/
}

?>