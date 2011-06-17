<?php

/*
 * class Controllers_Install
 */

class Controllers_Install {
    
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
        
        //Actie aanroepen. Dus: als www.skynet.nl/test/ dan testAction();
        if(isset($_GET['page']))
        {
            //remove install/ from begin of string and change / to "_"
            $_GET['page'] = str_replace("/","_",str_replace("install/","",$_GET['page']));
            //substr last char since that is always a /
            $action = strtolower(substr($_GET['page'],0,-1)).'Action';
            if(method_exists($this,$action))
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
    
    private function indexAction()
    {
        header("Location: /install/step/1/");
        $this->view->assign('contentTpl', 'install');
        $this->view->draw('main');
    }
    
    private function testAction()
    {
        $this->view->assign('contentTpl', 'install');
        $this->view->draw('main');
    }
    
    private function step_1Action(){
        $this->reg->installer->showSettings();
        $this->view->assign("content",$this->reg->installer->output);
        $this->view->draw('main');
    }
    private function step_2Action(){
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            foreach($_POST as $key => $value){
                $eKey = explode("_",$key);
                $type = $eKey[0];
                $setting = $eKey[1];
                $newSet[$type][$setting] = $value;
            }
            
            $sets = $this->reg->settings->settings;
            foreach($sets as $type=>$val){
                if(is_array($val)){
                    foreach($val as $setting=>$val2){
                        if(isset($newSet[$type][$setting])){
                            $sets[$type][$setting] = $newSet[$type][$setting];
                        }
                    }
                }
            }
            
            
        
        rename(basedir .'settings'. DS .'settings.json',basedir .'settings'. DS .'settings.json.old');
        $this->reg->settings->write_json_file($sets,basedir .'settings'. DS .'settings.json');
        $nextForm = <<<HTML
<form action="/install/step/3/" method="POST">
<input type="submit" value='Next step' />
</form>
HTML;
        $this->view->assign("content","Created the new settings.json file<br />" . $nextForm);
        $this->view->draw('main');
        } else {
            header("Location: /install/step/1/");
            exit();
        }
    }
    private function step_3Action(){
        if($_SERVER['REQUEST_METHOD'] === "POST"){
        $this->reg->installer->checkTables();
        $this->reg->installer->createAdminAccount("test","test","test");
        $this->view->assign("content",$this->reg->installer->output);
        $this->view->draw('main');
        } else {
            header("Location: /install/step/1/");
            exit();
        }
    }
   
}


?>