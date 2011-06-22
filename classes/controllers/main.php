<?php

/**
 * main.php
 * @package Skynet_controllers
*/
/**
 * class Controllers_Install
 * handles smart URL's for the installer
 * @version 0.1
 * @author Thom Werring <info@werringweb.nl>
 * @author Lucas Weijers <meel_to_lucas@hotmail.com>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_controllers
 */

class Controllers_Main {
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg = null;
    /**
     *$view
     *Direct link to view class. (From registry).
     *@access private
     */
    private $view;
    /**
     * __construct
     * creates base for CMS
    */
    function __construct() {
        $this->reg = Core_Registery::singleton();
        $this->reg->controller = $this;
        $this->view = $this->reg->view;

        //Actie aanroepen. Dus: als www.skynet.nl/test dan TestAction();
        if(isset($_GET['page']))
        {
            //remove install/ from begin of string and change / to "_"
            $action = str_replace("/","_",$_GET['page']);
            //substr last char since that is always a /
            $action = strtolower(substr($action,0,-1)).'Action';
            if(method_exists($this, $action))
            {
                call_user_func(array($this,$action));
            }
            else
            {
                $this->indexAction();
            }
        }
        else
        {
            $this->indexAction();
        }
    }
    
    /**
     * indexAction
     * action triggered on all pages on the main website,
     * unless a own method is to be found for it.
     * @access private
    */
    private function indexAction()
    {
        $page = isset($_GET['page']) ? substr($_GET['page'],0,-1) : "home" ;
        $this->reg->pages->getPageContent(strtolower($page));
    }

    
}
