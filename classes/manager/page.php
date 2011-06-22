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
 * @see Manager_Base()
 */

class Manager_Page extends Manager_Base {
    
    /**
     * __construct
     * Manager constructor
    */
    function __construct() {
        $this->init("pages");
        
    }
    /**
     * getPageContent
     * sends pageContent of given page to rainTPL
     * @access public
     * @param String $page page name
     * @todo make 404 page redirect in this function
    */
    public function getPageContent($page){
        
        $pages = $this->reg->database->prefixTable("pages");
        $templates = $this->reg->database->prefixTable("templates");
        $pcontent = $this->reg->database->prefixTable("pagecontent");
        $artikelen = $this->reg->database->prefixTable("artikelen");
        $forms = $this->reg->database->prefixTable("forms");
        $links = $this->reg->database->prefixTable("links");
        $cat = $this->reg->database->prefixTable("categorieen");
        
        $query = "SELECT
        $templates.Locatie,
        $artikelen.Titel as aTitle,
        $artikelen.Content as aContent,
        $artikelen.LastUpdate as aCreated,
        
        $forms.Naam as fNaam,
        $forms.Fields as fFields,
        
        $links.Naam as lNaam,
        $links.Titel as lTitel,
        $links.URL as lURL,
        
        cART.Titel as caTitel,
        cART.content as caContent,
        cART.LastUpdate as caCreated,
        
        $pcontent.type
        
        FROM `$pages`
                    inner join `$pcontent` on $pages.ID=$pcontent.pID
                    
                    left join `$artikelen` on $pcontent.aID=$artikelen.ID
                    
                    left join `$forms` on $pcontent.aID=$forms.ID
                    
                    left join `$cat` on $pcontent.aID=$cat.ID
                    left join `$artikelen` as cART on $cat.ID=cART.cID
                    left join `$links` on $cat.ID=$links.cID
                    
                    inner join `".$templates."` on ".$templates.".ID=".$pages.".tID
                    WHERE `".$pages."`.`Naam`='".$page."'";
        $result = $this->reg->database->query($query);
        if($result['affected']==0){
            return $this->getPageContent('home');
        }
        $contents = array();
        foreach($result as $key=>$content){
            if(!is_numeric($key)) continue;
                $templateLoc = $content['Locatie'];
                switch($content['type']){
                    case '1':
                        unset($content['Locatie']);
                        foreach($content as $field=>$value){
                            switch($field){
                                case 'aTitle':
                                    $artikel["Titel"] = $value;
                                break;
                                case 'aContent':
                                    $artikel["Content"] = nl2br($value);
                                break;
                                case 'aCreated':
                                    $artikel["Created"] = date("d-m-y H:i:s",$value);
                                break;
                            }
                        }
                        $ok = true;
                        for($i=0;$i<count($contents);$i++){
                            if($contents[$i]['Created'] == $artikel['Created']){
                                $ok = false;
                            }
                        }
                        if($ok) $contents[] = $artikel;
                        
                    break;
                    case '2':
                        foreach($content as $field=>$value){
                            switch($field){
                                case 'caTitel':
                                    $catagory["Titel"] = $value;
                                    break;
                                case 'caContent':
                                    $catagory["Content"] = nl2br($value);
                                    break;
                                case 'caCreated':
                                    $catagory["Created"] = date("d-m-y H:i:s",$value);
                                    break;
                            }
                        }
                        $contents[] = $catagory;
                    break;
                    case '3':
                        foreach($content as $field=>$value){
                            switch($field){
                                case 'fNaam':
                                    $form["Naam"] = $value;
                                    break;
                                case 'fFields':
                                    $form["Content"] = $value;
                                    break;
                            }
                        }
                        $contents[] = $catagory;
                    break;
                }
        }
        
        unset($query,$result);
        $result = $this->reg->database->select("pages","Naam","WHERE $pages.Zichtbaar=1 ORDER BY `$pages`.`Positie` ASC");
        
        $menu = array();
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
        
        return true;
    }
    
    /**
     * newPage
     * creates a new page in the database
     * @access public
     * @param String $name name of the new page
     * @param Integer $positie position in the menu
     * @param Boolean $zichtbaar is page visible in the menu
     * @param Integer $template template ID
     * @return Boolean success
    */
    public function newPage($name,$positie,$zichtbaar,$template){
        $table = 'pages';
        $data['Naam'] = $name;
        $data['Positie'] = $positie;
        $data['Zichtbaar'] = ($zichtbaar) ? 1 : 0;
        $data['tID'] = $template;
        return $this->reg->database->insert($table,$data);
    }
    
    /**
     * modPage
     * Modifies a page in the database
     * @access public
     * @param Integer $id ID of the page
     * @param Array Assoc Array with new data
     * @param Boolean succes
    */
    public function modPage($id,$data){
        $table = 'pages';
        $data['ID'] = $id;
        $result = $this->reg->database->delete($table,$data);
        return $result['succes'];
    }
    
    /**
     * deletePage
     * Deletes a page from the database
     * @param Integer $id id of the page
     * @return boolean succes
    */
    public function deletePage($id){
        $table = 'pages';
        $data['ID'] = $id;
        $result = $this->reg->database->delete($table,$data);
        unset($data);
        $table = 'pagecontent';
        $data['pID'] = $id;
        $result2 = $this->reg->database->delete($table,$data);
        return ($result['succes'] && $result2['succes']);
    }
    
    /**
     * linkContent
     * Links Content to a page
     * @param Integer $pageID ID of the page
     * @param Integer $contentID ID of the content resource
     * @param Integer $contentType type of content
     * @return Boolean succes
    */
    public function linkContent($pageID,$contentID,$contentType){
        $table = 'pagecontent';
        $data['pID'] = $pageID;
        $data['aID'] = $contentID;
        $data['type'] = $contentType;
        return $this->reg->database->insert($table,$data);
    }
    
    /**
     * unlinkContent
     * unlinks content from a page
     * @param Integer $pageID ID of the page
     * @param Integer $contentID ID of content resource
     * @param Integer $contentType Type of content
     * @access public
     * @return Boolean succes
    */
    public function unlinkContent($pageID,$contentID,$contentType){
        $table = 'pagecontent';
        $data['pID'] = $pageID;
        $data['aID'] = $contentID;
        $data['type'] = $contentType;
        $result = $this->reg->database->delete($table,$data);
        return $result['succes'];
    }
    
    
    /**
     * listPages
     * gets all pages from the database
     * @access public
     * @return Array list of pages with info
    */
    public function listPages(){
        $table = 'pages';
        
        $pcontent = $this->reg->database->prefixTable("pagecontent");
        $pages = $this->reg->database->prefixTable("pages");
        $templates = $this->reg->database->prefixTable("templates");
        //select from pages
        $select[] = "$pages.ID as pID";
        $select[] = "$pages.Naam as pNaam";
        $select[] = "$pages.Zichtbaar as pZichtbaar";
        //select from pagecontent
        #nothing
        //select from templates
        $select[] = "$templates.ID as tID";
        $select[] = "$templates.Naam as tNaam";
        $select[] = "$templates.Description as tDesc";
        
        $select = implode(",",$select);
        
        
        
        $advanced = "left join `$pcontent` on $pages.ID=$pcontent.pID" . PHP_EOL;
        $advanced.= "inner join `$templates` on $pages.tID=$templates.ID" . PHP_EOL;
        $result = $this->reg->database->select($table,$select,$advanced);
        return $result;
    }
    public function getCats(){
        $result = $this->reg->database->select("categorieen","ID,Naam");
        foreach($result as $key=>$value){
            if(!is_numeric($key)) continue;
            $cats[] = $value['Naam'] . " (Category) |" . $value["ID"] . "_2";
        }
        return $cats;
    }
    
    public function getArtikelen(){
        $result = $this->reg->database->select("artikelen","ID,Titel");
        foreach($result as $key=>$value){
            if(!is_numeric($key)) continue;
            $arts[] = $value['Titel'] . " (Article) |" . $value["ID"] . "_1";
        }
        return $arts;
    }
    
    public function getTemplates(){
        $result = $this->reg->database->select("templates","ID,Naam");
        foreach($result as $key=>$value){
            if(!is_numeric($key)) continue;
            $tpls[] = $value['Naam'] . "|" . $value["ID"];
        }
        return $tpls;
    }
}
