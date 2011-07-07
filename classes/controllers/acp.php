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
        $this->view->add_css('main.css');
        
        if($this->reg->session->checkCurrent() || (isset($_GET['page']) && $_GET['page'] == 'acp/login/')){
            if(isset($_GET['page']))
            {
                
                $action = str_replace("/","_",str_replace("acp/","",$_GET['page']));
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
        if($this->reg->session->checkCurrent()){
            header("Location: /acp/");
            exit();
        }
        if($_SERVER['REQUEST_METHOD']=="POST"){
            $name = $_POST['account'];
            $pass = sha1($_POST['pass']);
            $loggedIn = $this->reg->session->create($name,$pass);
            if($loggedIn){
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
    private function mngr_articleAction($subPage=null)
    {
        $aMngr = new Manager_Article();
        $pMngr = new Manager_Page();
        
        $actions = array( "New Article" => "mngr/article/new/","Article overview"=> "mngr/article/");
        
        switch($subPage){
            case 'delete':
                if(isset($_GET['id'])){
                    $aMngr->remove($_GET['id']);
                }
            case null:
            default:
                foreach($pMngr->getArtikelen() as $artikel){
                     $eArt = explode(" (Article) |",$artikel);
                     $aList[] = array("Name"=>$eArt[0],"ID"=>substr($eArt[1],0,-2));
                }
                $cmsContent = 'articleOverview';
                $this->view->assign("pageList",$aList);
                break;
            case 'new':
                $frm = $aMngr->checkForm(array("Title"=>"Title","content"=>"Content"),"add");
                
                if($frm === true){
                    $msg = $aMngr->add($_POST['Title'],$_POST['content'],$_POST['cat']);
                    if($msg==true){
                        $this->view->assign("msg",array("header"=>"Your page has been created."));
                    } else {
                        $this->view->assign("msg",array("header"=>"Something went wrong while creating your article"));
                        $this->view->assign("title",$_POST['Title']);
                        $this->view->assign("content",$_POST['content']);
                    }
                }
                if(isset($frm['header']))
                {
                    $this->view->assign('msg', $frm);
                    $this->view->assign("title",$_POST['Title']);
                    $this->view->assign("content",$_POST['content']);
                }
                foreach($pMngr->getCats() as $cat){
                    $eCat = explode(" (Category) |",$cat);
                    $cList[] = array("Name"=>$eCat[0],"ID"=>substr($eCat[1],0,-2));
                }
                $csMenu="";
                foreach($cList as $value){
                    $csMenu .= "<option value='$value[ID]'>$value[Name]</option>" . PHP_EOL;
                }
                $this->view->assign("cList",$csMenu);
                $cmsContent = 'articleAdd';
                break;
            case 'edit':
                $cmsContent="articleEdit";
                $frm = $aMngr->checkForm(array("aID"=>"An known error occured","Title"=>"Title","content"=>"Content"),"update");
                if($frm === true){
                    $msg = $aMngr->update($_POST['aID'],$_POST['Title'],$_POST['content'],$_POST['cat']);
                    if($msg==true){
                        $this->view->assign("msg",array("header"=>"Your page has been Updated."));
                    } else {
                        $this->view->assign("msg",array("header"=>"Something went wrong while updating your article"));
                    }
                }
                if(isset($frm['header']))
                {
                    $this->view->assign('msg', $frm);
                }
                
                foreach($pMngr->getCats() as $cat){
                    $eCat = explode(" (Category) |",$cat);
                    $cList[] = array("Name"=>$eCat[0],"ID"=>substr($eCat[1],0,-2));
                }
                
                $article = $aMngr->getArticlebyAID($_GET['id']);
                $currentCID = $article['cID'];
                $csMenu="";
                foreach($cList as $value){
                    if($currentCID==$value["ID"]){
                        $csMenu .= "<option selected='selected' value='$value[ID]'>$value[Name]</option>" . PHP_EOL;
                    } else {
                        $csMenu .= "<option value='$value[ID]'>$value[Name]</option>" . PHP_EOL;
                    }
                }
                
                
                $this->view->assign('aID', $_GET['id']);
                $this->view->assign("cList",$csMenu);
                $this->view->assign("content",$article['Content']);
                $this->view->assign("title",$article['Titel']);
                break;
        }
        
        $this->view->assign('cmsActions', $actions);
        $this->view->assign('contentTpl', $cmsContent);
        $this->view->draw('main');
    }
    
    private function mngr_pageAction($arguments=null){
        $pMngr = new Manager_Page();
        $actions = array("Page overview"=> "mngr/page/", "New page" => "mngr/page/new/", "Link Content" => "mngr/page/link/");
        is_null($arguments) ? $arguments[0] = null : "";
        
        $cmsContent=null;
        switch($arguments[0]){
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
                                    $frm = array();
                                    $frm['header'] = 'The following errors took place: ';
                                    $frm[0] = $this->reg->database->lastError();
                                    echo $this->reg->database->lastError();
                                }
                            } else {
                                $frm = array();
                                $frm['header'] = 'The following errors took place: ';
                                $frm[0] = $this->reg->database->lastError();
                                echo $this->reg->database->lastError();
                            }
                        } else {
                            $frm = array();
                            $frm['header'] = 'The following errors took place: ';
                            $frm[0] = $this->reg->database->lastError();
                            echo $this->reg->database->lastError();
                        }
                    }
                }
                if(isset($frm) && $frm===true){
                    $frm=array();
                    $frm['header'] = 'We succesfully added your page.';
                    
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
            case 'edit':
                
                $frm = $pMngr->checkForm(array("pID"=>"page ID"),"Delete");
                if($frm===true){
                    
                    $cDeleteID = $_POST['cDelete'];
                    $pMngr->unlinkContent($cDeleteID);
                    unset($frm);
                }
                $frm = $pMngr->checkForm(array("pName"=>"Page Name"),"Update");
                if($frm===true){
                    $pID = $_POST['pID'];
                    $data['Naam'] = $_POST['pName'];
                    $data['Positie'] = $_POST['position'];
                    $data['Zichtbaar'] = ($_POST['visible'] == 'true') ? 1 : 0;
                    $data['tID'] = $_POST['template'];
                    $pMngr->modPage($pID,$data);
                }
                
                $cmsContent = 'pageEdit';
                $page = $pMngr->getPageContentByID($_GET['id']);
                $option="";
                foreach($page as $key=>$value){
                    if(!is_numeric($key)) continue;
                    //print_r($value);
                    $type = $value['type'];
                    $pcID = $value['pcID'];
                    $pName = $value['pNaam'];
                    $pZicht = $value['pVis'];
                    $pPos = $value['pPos'];
                    switch($type){
                        case '1':
                            $naam = $value['aNaam'];
                            $option .= "<option value='$pcID'>$naam - Article</option>" . PHP_EOL;
                            break;
                        case '2':
                            $naam = $value['cNaam'];
                            $option .= "<option value='$pcID'>$naam - Catagory</option>" . PHP_EOL;
                            break;
                        case '3':
                            $naam = $value['lNaam'];
                            $option .= "<option value='$pcID'>$naam - Link</option>" . PHP_EOL;
                            break;
                        case '4':
                            $naam = $value['fNaam'];
                            $option .= "<option value='$pcID'>$naam - Form</option>" . PHP_EOL;
                            break;
                    }
                    
                }
                
                $tempList = $pMngr->getTemplates();
                $tsMenu = "";
                foreach($tempList as $value){
                    $eVal = explode("|",$value);
                    $sValue = array_pop($eVal);
                    $sName  = implode("|",$eVal); 
                    $tsMenu .= "<option value='$sValue'>$sName</option>" . PHP_EOL;
                }
                $this->view->assign("templateSelectMenu",$tsMenu);
                $this->view->assign('contentList', $option);
                $this->view->assign('pID',$_GET['id']);
                $this->view->assign('pVis',$pZicht);
                $this->view->assign('pagePos',$pPos);
                $this->view->assign('pageName',$pName);
                
                break;
        }
        
        $this->view->assign('cmsActions', $actions);
        $this->view->assign('contentTpl', $cmsContent);
        $this->view->draw('main');
    }
    /**
     * mngr_moduleAction
     * action triggerd when someone looks at the /user/module
     * @access private
    */
    private function mngr_moduleAction($arg=null)
    {
        $mMngr = new Manager_Module();
        $actions = array("Module list"=> "mngr/module", "Add module" => "mngr/module/add", "General Module settings" => "mngr/module/settings");
        switch($arg)
        {
            case 'moduleList':
            default:
                $cmsContent = 'moduleList';
                $this->view->assign("msg", "Er zijn geen modules beschikbaar");
                                
                $activeModules = $mMngr->getModules('active');
                $nonactiveModules = $mMngr->getModules('non-active');
                if($activeModules !== false)
                {
                    $this->view->assign("ActiveModules", $activeModules);
                }
                
                if($nonactiveModules !== false)
                {
                    $this->view->assign("nonActiveModules", $nonactiveModules);
                }
                
                break;
            
            case 'add';
                $cmsContent = 'moduleAdd';
                $this->view->assign("msg", "Er zijn geen modules beschikbaar om te installeren");                
                if(!empty($_GET['dirname']))
                {
                    $mMngr->installModule($_GET['dirname']);
                }
                
                $modules = $mMngr->checkInstall(); #check for nieuwe module installaties
                if($modules !== false)
                {
                    $this->view->assign("modules", $modules);
                }
                break;
            
            case 'activate':
                $mMngr->activateModule($_GET['dirname']);
                $cmsContent = 'moduleList';
                $this->view->assign("msg", "Er zijn geen modules beschikbaar");
                                
                $activeModules = $mMngr->getModules('active');
                $nonactiveModules = $mMngr->getModules('non-active');
                if($activeModules !== false)
                {
                    $this->view->assign("ActiveModules", $activeModules);
                }
                
                if($nonactiveModules !== false)
                {
                    $this->view->assign("nonActiveModules", $nonactiveModules);
                }
                break;
            
            case 'deactivate':
                $mMngr->deactivateModule($_GET['dirname']);
                $cmsContent = 'moduleList';
                $this->view->assign("msg", "Er zijn geen modules beschikbaar");
                                
                $activeModules = $mMngr->getModules('active');
                $nonactiveModules = $mMngr->getModules('non-active');
                if($activeModules !== false)
                {
                    $this->view->assign("ActiveModules", $activeModules);
                }
                
                if($nonactiveModules !== false)
                {
                    $this->view->assign("nonActiveModules", $nonactiveModules);
                }
                break;
            
            case 'uninstall':
                $mMngr->uninstallModule($_GET['dirname']);
                $cmsContent = 'moduleList';
                $this->view->assign("msg", "Er zijn geen modules beschikbaar");
                                
                $activeModules = $mMngr->getModules('active');
                $nonactiveModules = $mMngr->getModules('non-active');
                if($activeModules !== false)
                {
                    $this->view->assign("ActiveModules", $activeModules);
                }
                
                if($nonactiveModules !== false)
                {
                    $this->view->assign("nonActiveModules", $nonactiveModules);
                }
                break;
            
        }
        $this->view->assign('cmsActions', $actions);
        $this->view->assign('contentTpl', $cmsContent);
        $this->view->draw('main');
        
        
    }
}