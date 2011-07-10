<?php
/**
 * test.php
 * @package Skynet_Managers
*/
/**
 * Class Manager_Test
 * 
 * @version 0.1
 * @author Thom Werring <info@werringweb.nl>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_Managers
*/

class Manager_Test extends Manager_Base
{
    
    public static $name = "Test Manager";
    
    /**
     * __construct
     * Constructor to load manager
    */
    
    function __construct()
    {
        $this->init("test");
        
    }
    
    public function page($args){
        count($args)==0 ? $args[0] = 'main' : false;
        switch($args[0]){
            case 'tester':
                $this->reg->view->assign("content","Tester page for tester module");
            break;
            default:
                $this->reg->view->assign("content","main page for tester module");
            break;
        }
        
        BAD CODE IN FILE;
    }
}

?>