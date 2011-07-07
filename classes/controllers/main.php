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
            $action = str_replace("/","_",$_GET['page']);
            $subActions = explode("_",$action);
            array_pop($subActions);
            $count = count($subActions);
            $args = array();
            $actionCalled = false;
            for($i=0;$i<$count;$i++){
                $subAction = implode("_",$subActions) . "Action";
                if(method_exists($this, $subAction ))
                {
                    if(count($args) == 0 ){
                        call_user_func(array($this,$subAction));
                    } else {
                        $args = array_reverse($args);
                        call_user_func(array($this,$subAction), $args);
                    }
                    $actionCalled = true;
                    $this->reg->debug->msg("core","controller","Calling " . $subAction . " with arg: " . var_export($args,true),__CLASS__.":".__LINE__);
                    break;
                }
                $args[] = array_pop($subActions);
            }
            if(!$actionCalled){
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
