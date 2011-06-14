<?php

/*
 * class Core_Registery
 * singleton
 */

class Core_Registery {
    private static $instance;
    
    private $classes = null;
    
    static function singleton(){
        if(is_null(self::$instance)){
            $c = __CLASS__;
            new $c;
        }
        
        return self::$instance;
    }
    
    function __construct(){
        if(!is_null(self::$instance)){
            die("Use " . __CLASS__ . "::singleton() instead of new " . __CLASS__ . "()");
        } else {
            self::$instance = $this;
        }
        $this->init();
    }
    
    private function init(){
        
    }
    
    function __set($name, $value){
        $this->classes[$name] = $value;
    }
    
    function __get($name){
        if(isset($this->classes[$name]))
            return $this->classes[$name];
        else
            return null;
    }
    
}