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
        
    function __construct() {
        $this->init("user");
    }
    
    public function add($Titel,$Content,$cID=1){
        $tabel              = 'artikelen';
        $data['cID']        = $cID;
        $data['Titel']      = $Titel;
        $data['Content']    = $Content;
        $data['LastUpdate'] = time();
        return $this->reg->database->insert($tabel,$data);
    }
    
    public function update($aID,$Titel=null,$Content=null,$cID=null){
        $tabel              = 'artikelen';
        $data['cID']        = $cID;
        $data['Titel']      = $Titel;
        $data['Content']    = $Content;
        $data['LastUpdate'] = time();
        foreach($data as $key=>$update){
            if(is_null($update)) continue;
            $UP[$key] = $update;
        }
        if(count($UP)>1){
            $result = $this->reg->database->update($tabel,$data,array("ID"=>$aID));
            return $result['succes'];
        } else {
            return true;
        }
    }
    
    public function remove($aID){
        $result = $this->reg->database->delete('artikelen',array('ID'=>$aID));
        return $result['succes'];
    }
    
}