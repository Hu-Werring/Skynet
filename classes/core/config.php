<?php

/*
 * class Core_Config
 */

class Core_Config  {
    
    private $reg;
    
    private $storage;
    
    function __construct() {
        $this->reg = Core_Registery::singleton();
        $this->reg->config = $this;
        $this->storage["db_host"] = "localhost";
        $this->storage["db_user"] = "project4";
        $this->storage["db_pass"] = "project4";
        $this->storage["db_daba"] = "project4";
        $this->storage["cms_debug"] = true;
        
    }
    
    public function getvar($name){
        if(isset($this->storage[$name])){
            return $this->storage[$name];
        } else {
            return null;
        }
    }
}

?>