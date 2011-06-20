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
        $this->view->assign('contentTpl', 'install');
        $this->view->assign('logo', '<img src="/images/logo.png" alt="Skynet" />');
        $this->view->add_js('/script/jquery.js');
        $this->view->add_js('/install/script/install.js');
        $this->view->add_css('/install/style/install.css');
        $this->view->add_css('/view/css/style.css');
        //Actie aanroepen. Dus: als www.skynet.nl/test/ dan testAction();
        if(isset($_GET['page']))
        {
            //remove install/ from begin of string and change / to "_"
            $action = str_replace("/","_",str_replace("install/","",$_GET['page']));
            //substr last char since that is always a /
            $action = strtolower(substr($action,0,-1)).'Action';
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
                if($value==="true"){
                    $value=true;
                }
                if($value==="false"){
                    $value=false;
                }
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
        $this->reg->installer->nextStep(3);
        $this->view->assign("content","<fieldset class='step'><legend>Step 2 - Update settings</legend>Created the new settings.json file</fieldset><div class='clear' id='submitButton'>" . $this->reg->installer->output . "</div>");
        $this->view->draw('main');
        } else {
            header("Location: /install/step/1/");
            exit();
        }
    }
    private function step_3Action(){
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            if(isset($_POST['force']) && $_POST['force']=='true'){
                $force=true;
            } else {
                $force=false;
            }
            $this->reg->installer->checkTables($force);
            $this->reg->installer->nextStep(4);
            $this->view->assign("content","<fieldset class='step'><legend>Step 3</legend>" . $this->reg->installer->output . "</div>");
            $this->view->draw('main');
        } else {
            header("Location: /install/step/1/");
            exit();
        }
    }
   
    private function step_4Action(){
        
        $this->view->assign("content", $this->reg->installer->output);
        
        $this->view->draw('main');
    }
   
}


?>