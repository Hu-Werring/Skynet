<?php

/**
 * page.php
 * @package Skynet_managers
*/
/**
 * class Manager_Page
 * Base class for all managers
 * @version 0.1
 * @author Thom Werring <info@werringweb.nl>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_managers
 */

class Manager_Page extends Manager_Base {
        
    function __construct() {
        $this->init("pages");
        
    }
    
    function getPageContent($page){
        
        $pages = $this->reg->database->prefixTable("pages");
        $templates = $this->reg->database->prefixTable("templates");
        $pcontent = $this->reg->database->prefixTable("pagecontent");
        $artikelen = $this->reg->database->prefixTable("artikelen");
        
        $query = "SELECT ".$templates.".Locatie,".$artikelen.".Titel,".$artikelen.".Content,".$artikelen.".LastUpdate FROM `".$pages."`
                    inner join `".$pcontent."`on ".$pcontent.".pID=".$pages.".ID
                    inner join `".$artikelen."`on ".$pcontent.".aID=".$artikelen.".ID
                    inner join `".$templates."` on ".$this->reg->database->prefixTable("templates").".ID=".$this->reg->database->prefixTable("pages").".tID
                    WHERE `".$pages."`.`Naam`='".$page."'";
        $result = $this->reg->database->query($query);
        
        foreach($result as $key=>$content){
            if(!is_numeric($key)) continue;
                $templateLoc = $content['Locatie'];
                unset($content['Locatie']);
                $contents[] = $content;
        }
        
        unset($query,$result);
        $result = $this->reg->database->select("pages","Naam","ORDER BY `$pages`.`Positie` ASC");
        
        
        foreach($result as $key=>$menuItem){
            if(!is_numeric($key)) continue;
            if($menuItem['Naam']===$page){
                $menuItem['Current'] = true;
            } else {
                $menuItem['Current'] = false;
            }
            $menu[] = $menuItem;
        }
        $this->reg->view->assign('contentTpl', $templateLoc);
        $this->reg->view->assign('content',$contents);
        $this->reg->view->assign('menu',$menu);
        $this->reg->view->draw('main');
    }

}
?>