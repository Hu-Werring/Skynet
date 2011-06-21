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
 */
class Manager_Base {
    
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
}
?>