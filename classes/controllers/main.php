<?php

/*
 * class Controllers_Main
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
        
        //Actie aanroepen. Dus: als www.skynet.nl/test/ dan TestAction();
        if(isset($_GET['page']))
        {
            //substr last char since that is always a /
            $action = str_replace("/","_",$_GET['page']);
            $action = strtolower(substr($action,0,-1)).'Action';
            if(method_exists($this,$action))
            {
                call_user_func(array($this,$action));
            }
            else
            {
                $this->indexAction();
            }
            //print_r($_GET)
        }
        else
        {
            $this->indexAction();
        }
    }
    
    private function indexAction()
    {
        $this->view->assign('content', 'grapje');
        $this->view->assign('contentTpl', 'home');
        $this->view->draw('main');
    }
    
    private function testAction()
    {
        $this->view->assign('contentTpl', 'home');
        $this->view->assign('content', 'grapjes');
        $this->view->draw('main');
    }
    
   
}

?>