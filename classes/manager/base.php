<?php

/**
 * base.php
 * @package Skynet_managers
*/
/**
 * class Manager_Base
 * Base class for all managers
 * @version 0.1
 * @author Thom Werring <info@werringweb.nl>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_managers
 * @abstract
 */
abstract class Manager_Base {
    
    public static $name = "Nameless Manager";
    public static $desc = "~No description~";
    /**
     * $reg
     * registery holder
    */
    protected $reg = null;
    /**
     * __construct()
     * Init page Manager
     */
    function __construct() {
        
    }
    
    protected function init($className){
        $this->reg = Core_Registery::singleton();
        if(!isset($this->reg->managers)){
            $this->reg->managers = new stdClass();
        }
        $this->reg->managers->$className = $this;
    }
    
    protected function cmsActions($actions)
    {
        $this->reg->view->assign("cmsActions", $actions);
    }
    
    public function checkForm($verplicht, $trigger)
    {
        if(isset($_POST[$trigger]))
        {
            $errorItem = array();
            foreach($verplicht as $key => $value)
            {
                if(empty($_POST[$key]))
                {
                    $errorItem[] = $value;
                }
            }
            
            $msg['items'] = $errorItem;
            
            if(count($errorItem) > 0)
            {
                $msg['header'] = 'The following fields where left blank or where not entered correctly: ';
                return $msg;
            }
            else
            {
                return true;
            }
        }
    }
    
    public function page($args){
        $this->reg->view->assign("content", "There is no page for this manager, someone did a boo boo.");
    }
}
?>