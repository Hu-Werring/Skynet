<?php

/*
 * class Controllers_Acp
 * Handles the smart URL's for the Admin Panel
 * @version 0.1
 * @author Lucas Weijers <meel_to_lucas@hotmail.com>
 * @copyright Copyright (c) 2011, Lucas Weijers & Thom Werring
 */

class Controllers_Acp {
    
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

        //Actie aanroepen. Dus: als www.skynet.nl/acp/test dan TestAction();
        if(isset($_GET['page']))
        {
            //remove install/ from begin of string and change / to "_"
            $action = str_replace("/","_",str_replace("acp/","",$_GET['page']));
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
     * action triggered on all pages in the ACP,
     * unless a own method is to be found for it.
     * @access private
    */
    private function indexAction()
    {
        $this->view->add_css('style.css');
        $this->view->assign('contentTpl', 'overview');
        //$this->view->assign('content', 'grapje');
        $this->view->draw('main');
    }
    /**
     * userAction
     * action triggerd when someone looks at the /user/ page
     * @access private
    */
    private function userAction()
    {
        $this->view->assign('contentTpl', 'overview');
       // $this->view->assign('content', 'HAha test');
        $this->view->draw('main');
    }
    
    
}