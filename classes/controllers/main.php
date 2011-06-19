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

        //Actie aanroepen. Dus: als www.skynet.nl/test dan TestAction();
        if(isset($_GET['page']))
        {
            $action = strtolower(str_replace("/", "",$_GET['page'])).'Action';
            if(method_exists($this, $action))
            {
                $this->{$action}();
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
    
    private function indexAction()
    {
        $this->view->assign('contentTpl', 'home');
        $this->view->assign('content', 'grapje');
        $this->view->draw('main');
    }
    
    private function testAction()
    {
        $this->view->assign('contentTpl', 'test');
        $this->view->assign('content', 'HAha test');
        $this->view->draw('main');
    }
    
    
}

?>