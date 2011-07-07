<?php

/**
 * article.php
 * @package Skynet_managers
*/
/**
 * class Manager_Article
 * Article Manager class
 * @version 0.1
 * @author Thom Werring <info@werringweb.bl>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_managers
 */

class Manager_Article extends Manager_Base {
    
    public static $name = "Article Manager";
    public static $desc = "Manage your articles for you";
    
    function __construct() {
        $this->init("user");
    }
    
    public function add($Titel,$Content,$cID=1){
        $db = $this->reg->database;
        $tabel              = 'artikelen';
        $data['cID']        = $db->res($cID);
        $data['Titel']      = $db->res($Titel);
        $data['Content']    = $db->res($Content);
        $data['LastUpdate'] = time();
        return $db->insert($tabel,$data);
    }
    
    public function update($aID,$Titel=null,$Content=null,$cID=null){
        $db = $this->reg->database;
        $tabel              = 'artikelen';
        $data['cID']        = $db->res($cID);
        $data['Titel']      = $db->res($Titel);
        $data['Content']    = $db->res($Content);
        $data['LastUpdate'] = time();
        foreach($data as $key=>$update){
            if(is_null($update)) continue;
            $UP[$key] = $update;
        }
        if(count($UP)>1){
            $result = $db->update($tabel,$data,array("ID"=>$aID));
            return $result['succes'];
        } else {
            return true;
        }
    }
    
    public function remove($aID){
        $result  = $this->reg->database->delete('artikelen',array('ID'=>$aID));
        $result2 = $this->reg->database->delete('pagecontent',array('aID'=>$aID,"type"=>1));
        return ($result['succes'] && $result2["succes"]);
    }
    
    public function getArticlebyAID($aID){
        $result = $this->reg->database->select("artikelen","*", "WHERE ID='$aID'");
        if($result["affected"]>0){
            return $result[0];
        } else {
            return 0;
        }
    }
    
}