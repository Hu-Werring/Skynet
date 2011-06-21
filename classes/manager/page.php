<?php

/**
 * page.php
 * @package Skynet_managers
*/
/**
 * class Manager_Page
 * Base class for all managers
 * @version 0.1
 * @author Thom Werring <info@werringweb.nl>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_managers
 */

class Manager_Page extends Manager_Base {
        
    function __construct() {
        $this->init();
    }
    function test(){
        var_dump($this->reg);
    }
}
?>