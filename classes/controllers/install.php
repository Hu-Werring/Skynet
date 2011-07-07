<?php¤

/**
 * install.php
 * @package Skynet_controllers
*/

/**
 * class Controllers_Install
 * handles smart URL's for the installer
 * @version 0.5
 * @author Thom Werring <info@werringweb.nl>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_controllers
 */

class Controllers_Install {
    
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg = null;
    
    /**
     * $view
     * Direct link to view class. (From registry).
     * @access private
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
            $action = str_replace("/","_",str_replace("install/","",$_GET['page']));
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
     * action triggered on all pages in the ACP,
     * unless a own method is to be found for it.
     * Auto redirects to /install/step/1/
     * @access private
    */
    private function indexAction()
    {
        header("Location: /install/step/1/");
        $this->view->assign('contentTpl', 'install');
        $this->view->draw('main');
    }
    
    /**
     * step_1Action
     * First installer step
     * @see Core_Installer::showSettings()
     * @access private
    */
    private function step_1Action(){
        $this->reg->installer->showSettings();
        $this->view->assign("content",$this->reg->installer->output);
        $this->view->draw('main');
    }
    
    /**
     * step_2Action
     * Second installer step
     * handels POST data from step 1 and write it to a new settings.json
     * old settings are stored as settings.json.old
     * @access private
    */
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
        $this->view->assign("content","<fieldset class='step'><legend>Step 2 - Update settings</legend>Your new settings have been stored. Automatic continue in 3 seconds.</fieldset><div class='clear' id='submitButton'>" . $this->reg->installer->output . "</div>");
        $this->view->draw('main');
        } else {
            header("Location: /install/step/1/");
            exit();
        }
    }


    /**
     * step_3Action
     * Third step for the installer
     * Checks for database
     * @see Core_Installer::checkTables()
     * @see Core_Installer::createTables()
     * @access private
    */
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
   
    /**
     * step_4Action
     * fourth step for the installer
     * admin creating form
     * @see Core_Installer::createAdminAccountForm()
     * @access private
    */
    private function step_4Action(){
        if($_SERVER['REQUEST_METHOD'] !== "POST"){
            header("Location: /install/step/1/");
            exit();
        }
        $this->reg->installer->createAdminAccountForm();
        $this->view->assign("content", $this->reg->installer->output);
        
        $this->view->draw('main');
    }
    
    /**
     * step_5Action
     * fifth step for the installer
     * Creates actual admin account
     * and populates other tables
     * @see Core_Installer::createAdminAccount()
     * @access private
    */
    private function step_5Action(){
       if($_SERVER['REQUEST_METHOD'] !== "POST"){
            header("Location: /install/step/1/");
            exit();
        }
        //var_dump($_POST);
        $name = $_POST['name'];
        $email = $_POST['email'];
        $email2 = $_POST['emailCheck'];
        $pass = $_POST['pass'];
        $pass2 = $_POST['passCheck'];
        $errors = array();
        if(strlen($name) < 4){
            $errors[] = "Your name is to short, it should be at least 4 characters";
        }
        //lazy email check:
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors[] = "Your email is not valid";
        }
        if($email != $email2){
            $errors[] = "Your email doesn't match the second field";
        }
        if(strlen($pass)<8){
            $errors[] = "Your password is to short";
        }
        if($pass != $pass2){
            $errors[] = "Your password doesn't match the second field";
        }
        if(count($errors) == 0){
            $this->reg->installer->createAdminAccount($name,$email,$pass);
            $output = "<fieldset class='step'><legend>Step 5 - Creating Account</legend>Your account has been created<br/> you can now log in to the <a href='/acp/'>ACP</a>.";
        } else {
            $output = "<fieldset class='step'><legend>Step 5 - Creating Account</legend>Creating your account has failed the following error(s) took place:<ul>";
            foreach($errors as $error){
                $output .= "<li>" . $error;
            }
            $output .= "</ul></fieldset><div class='clear' id='submitButton'><form action='/install/step/4/' method='POST'><input type='submit' value='One step back' /></form></div>";
        }
        $this->view->assign("content", $output);
        $this->view->draw('main');
    }
   
}