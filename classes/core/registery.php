<?php
/**
 * registery.php
 * @package Skynet_core
*/
/**
 * class Core_Registery
 * singleton
 * Core registery class
 * @version 1.0
 * @author Thom Werring <info@werringweb.nl>
 * @author Lucas Weijers <meel_to_lucas@hotmail.com>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_core
*/

class Core_Registery {
    
    /**
     * $instance
     * stores link to itself for singleton instance
     * @access private
     * @staticvar Object singleton holder 
    */
    private static $instance;
    
    /**
     * $classes
     * Stores links to all other classes
     * @access private
     * @var Array links to other classes
    */
    private $classes = null;
    
    /**
     * singleton
     * Creates and/or returns instance of itself
     * @static
     * @return Object The current Core_Registery object
    */
    static function singleton(){
        if(is_null(self::$instance)){
            $c = __CLASS__;
            new $c;
        }
        
        return self::$instance;
    }
    
    /**
     * __construct
     * initiate registery
    */
    function __construct(){
        if(!is_null(self::$instance)){
            die("Use " . __CLASS__ . "::singleton() instead of new " . __CLASS__ . "()");
        } else {
            self::$instance = $this;
        }
    }
    
    /**
     * __set
     * magic function to created links to new classes
     * @param String $name Name of the class
     * @param Object $value Link to the object
    */
    public function __set($name, $value){
        $this->classes[$name] = $value;
    }
    
    /**
     * __get
     * magic function to call object stored in registery
     * @param String $name Name of the class
     * @return Object Link to the object
    */
    public function __get($name){
        if(isset($this->classes[$name]))
            return $this->classes[$name];
        else
            return null;
    }
}