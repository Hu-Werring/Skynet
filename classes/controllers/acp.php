<?php
/**
 * acp.php
 * @package Skynet_controllers
*/

/**
 * class Controllers_Acp
 * Handles the smart URL's for the Admin Panel
 * @package Skynet_controllers
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
        $this->view->add_css('style.css');
        if((isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']==true)  || (isset($_GET['page']) && $_GET['page'] == 'acp/login/')){

        //Actie aanroepen. Dus: als www.skynet.nl/acp/test dan TestAction();
        if(isset($_GET['page']))
        {
            
            $action = str_replace("/","_",str_replace("acp/","",$_GET['page']));
            $action2 = explode("_", $action);            
            $actionArgs = $action2[(count($action2)-2)]; #-2 cause last char is also a /
            $action2 = explode("_", $action, "-2"); 
            $action2 = implode("_", $action2);
            //substr last char since that is always a /
            $action = strtolower(substr($action,0,-1)).'Action';
            $action2 = strtolower($action2).'Action';

            if(method_exists($this, $action))
            {
                call_user_func(array($this,$action));
            }
            elseif(method_exists($this, $action2))
            {
                call_user_func(array($this,$action2), $actionArgs);
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
        } else {
            header("Location: /acp/login/");
            exit();
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
        $this->view->assign('contentTpl', 'overview');
        //$this->view->assign('content', 'grapje');
        $this->view->draw('main');
    }
    
    private function loginAction(){
        if($_SERVER['REQUEST_METHOD']=="POST"){
            $name = $_POST['account'];
            $pass = sha1($_POST['pass']);
            $result = $this->reg->database->select("users","COUNT(*)","WHERE Name='$name' AND Pass='$pass'");
            if($result['affected']>=1){
                $_SESSION['loggedIn']=true;
                header("Location: /acp/");
                exit();
            }
            
        }
        $this->view->assign('contentTpl', 'login');
        $this->view->draw('main');
    }
    /**
     * userAction
     * action triggerd when someone looks at the /user/ page
     * @access private
    */
    private function mngr_userAction($arg=null)
    {
        $uMngr = new Manager_User();
        $actions = array("User list"=> "mngr/user", "New user" => "mngr/user/new", "blacklist" => "mngr/user/blacklist");
        
        switch($arg)
        {
            case 'userlist':
            default:
                $cmsContent = 'userList';
                $users = $uMngr->getUsers();
                $this->view->assign('users', $users);
                break;
            
            case 'new':
                $frm = $uMngr->checkForm(array("name" => "Name", "pass" => "Password", "email" => "Email"), "submit");

                if($frm === true)
                {
                    $msg = $uMngr->createUser(array("Name" => $_POST['name'], "Pass" => sha1($_POST['pass']), "Email" => $_POST['email']));
                    if($msg === true)
                    {
                        $this->view->assign('msg', array("header" => 'Added user succesfully!!!'));
                    }
                    else
                    {
                        $this->view->assign('msg', array("header" => 'Adding user Failed!!!'));
                    }
                }
                
                if(isset($frm['header']))
                {
                    $this->view->assign('msg', $frm);
                }
                
                $cmsContent = 'userNew';
                break;
            
            case 'delete':
                if($_GET['id'] !== '1')
                {
                    $uMngr->deleteUser($_GET['id']);
                }
                
                $cmsContent = 'userList';
                $users = $uMngr->getUsers();
                $this->view->assign('users', $users);
                break;
            
            case 'edit':
                $user = $uMngr->getUser($_GET['id']);
                $user = $user[0];
                
                $frm = $uMngr->checkForm(array("name" => "Name", "pass" => "Password", "email" => "Email"), "submit");
                
                if($frm === true)
                {
                    $updateArgs = array();
                    $updateArgs['Name'] = $_POST['name'];
                    if($_POST['pass'] != 'dummypass'){ $updateArgs['Pass'] = sha1($_POST["pass"]); }
                    $updateArgs['Email'] = $_POST['email'];
                    
                    $msg = $uMngr->updateUser($_GET['id'], $updateArgs);

                    if($msg['succes'] === true)
                    {
                        $this->view->assign('msg', array("header" => 'Updated user succesfully!!!'));
                    }
                    else
                    {
                        $this->view->assign('msg', array("header" => 'Update Failed!!!'));
                    }
                }
                
                if(isset($user['ID']))
                {
                    $this->view->assign('user', $user);
                }
                else
                {
                    header("Location: /acp/mngr/user/");
                    exit();
                }
                
                if(isset($frm['header']))
                {
                    $this->view->assign('msg', $frm);
                }
                $cmsContent = 'userEdit';
                break;
        }
        $this->view->assign('cmsActions', $actions);
        $this->view->assign('contentTpl', $cmsContent);
        $this->view->draw('main');
        
        
    }
    private function mngr_pageAction($argument=null){
        $pMngr = new Manager_Page();
        $actions = array("Page overview"=> "mngr/page/", "New page" => "mngr/page/new/", "Link Content" => "mngr/page/link/", "Unlink Content" => "mngr/page/unlink/");
        
        
        $cmsContent=null;
        switch($argument){
            case null:
                $pages = $pMngr->listPages();
                for($i=0;$i<count($pages)-2;$i++){
                    $pageList[$pages[$i]['pNaam']] = array("tID"=>$pages[$i]['tID'],"Template"=>$pages[$i]['tNaam'],"pID"=>$pages[$i]['pID'],"pNaam"=>$pages[$i]["pNaam"]);
                    
                }
                $this->view->assign('pageList', $pageList);
                $cmsContent = "pageOverview";
                break;
            case 'new':
                $error=false;
                if($_SERVER['REQUEST_METHOD'] === "POST"){
                    $frm = $pMngr->checkForm(array("name"=>"Name","position"=>"Position"),'submit');
                    if($frm === true){
                        $name = $this->reg->database->res($_POST['name']);
                        $pos = $_POST['position'];
                        $template = $_POST['template'];
                        $visible = (isset($_POST['visible']) && $_POST['visible'] === "true") ? 1 : 0;
                        if($pMngr->newPage($name,$pos,$visible,$template)){
                            $res = $this->reg->database->select("pages","ID","WHERE Naam='$name'");
                            if($res["succes"]===true){
                                $pID = $res[0]['ID'];
                                $eContent = explode("_",$_POST['content']);
                                if(!$pMngr->linkContent($pID,$eContent[0],$eContent[1])){
                                    $frm['header'] = 'TThe following errors took place: ';
                                    $frm[] = $this->reg->database->lastError();
                                }
                            } else {
                                $frm['header'] = 'TThe following errors took place: ';
                                $frm[] = $this->reg->database->lastError();
                            }
                        } else {
                            $frm['header'] = 'TThe following errors took place: ';
                            $frm[] = $this->reg->database->lastError();
                        }
                    }
                }
                $cmsContent = 'pageNew';
                $artikelList = $pMngr->getArtikelen();
                $catList = $pMngr->getCats();
                $itemList = array_merge($artikelList,$catList);
                $tempList = $pMngr->getTemplates();
                
                $csMenu="";
                foreach($itemList as $value){
                    $eVal = explode("|",$value);
                    $sValue = array_pop($eVal);
                    $sName  = implode("|",$eVal); 
                    $csMenu .= "<option value='$sValue'>$sName</option>" . PHP_EOL;
                }
                
                
                $tsMenu = "";
                foreach($tempList as $value){
                    $eVal = explode("|",$value);
                    $sValue = array_pop($eVal);
                    $sName  = implode("|",$eVal); 
                    $tsMenu .= "<option value='$sValue'>$sName</option>" . PHP_EOL;
                }
                
                $this->view->assign("contentSelectMenu",$csMenu);
                $this->view->assign("templateSelectMenu",$tsMenu);
                if(isset($frm['header']))
                {
                    $this->view->assign('msg', $frm);
                } 
                break;
            case 'delete':
                if(isset($_GET['id']) && is_numeric($_GET['id'])){
                    $pMngr->deletePage($_GET['id']);
                }
                $pages = $pMngr->listPages();
                for($i=0;$i<count($pages)-2;$i++){
                    $pageList[$pages[$i]['pNaam']] = array("tID"=>$pages[$i]['tID'],"Template"=>$pages[$i]['tNaam'],"pID"=>$pages[$i]['pID'],"pNaam"=>$pages[$i]["pNaam"]);
                    
                }
                $this->view->assign('pageList', $pageList);
                $cmsContent = "pageOverview";
                break;
            case 'link':
                if($_SERVER['REQUEST_METHOD'] === "POST"){
                    $pID = $_POST['pid'];
                    $eContent = explode("_",$_POST['content']);
                    if(!$pMngr->linkContent($pID,$eContent[0],$eContent[1])){
                        $frm['header'] = 'TThe following errors took place: ';
                        $frm[] = $this->reg->database->lastError();
                    }
                }
                
                $arts = $pMngr->getArtikelen();
                $cats = $pMngr->getCats();
                $itemList = array_merge($arts,$cats);
                $plist = $pMngr->getPages();

                $csMenu="";
                foreach($itemList as $value){
                    $eVal = explode("|",$value);
                    $sValue = array_pop($eVal);
                    $sName  = implode("|",$eVal); 
                    $csMenu .= "<option value='$sValue'>$sName</option>" . PHP_EOL;
                }
                
                
                $psMenu = "";
                foreach($plist as $value){
                    
                    $eVal = explode("|",$value);
                    $sValue = array_pop($eVal);
                    $sName  = implode("|",$eVal); 
                    $psMenu .= "<option value='$sValue'>$sName</option>" . PHP_EOL;
                }
                $this->view->assign("pagesSelectMenu",$psMenu);
                $this->view->assign("contentSelectMenu",$csMenu);
                
                if(isset($frm['header']))
                {
                    $this->view->assign('msg', $frm);
                } 
                $cmsContent = "pageLink";
                break;
        }
        
        $this->view->assign('cmsActions', $actions);
        $this->view->assign('contentTpl', $cmsContent);
        $this->view->draw('main');
    }

}