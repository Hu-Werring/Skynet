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
        $this->reg->$className = $this;
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
}
?>