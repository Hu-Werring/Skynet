<?php

/*
 * class Core_Installer
 */

class Core_Installer {
    
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg = null;
    
    
    /**
     * Stores Output to send to template
    */
    public $output = "";
    /*
     * __construct()
     */
    
    function __construct() {
        $this->reg = Core_Registery::singleton();
        
        $this->reg->installer = $this;
        
        //$this->checkTables();
        //$this->createTables();
    }
    
    /**
     * showSettings
     * Sends settings in settings.json to $output in nice HTML format
    */
    public function showSettings(){
            $sets = $this->reg->settings->settings;
            $this->output.="<style>".PHP_EOL;
            $this->output.=<<<CSS
            .settings {
                width: 30%;
                float: left;
            }
            .clear{
                clear:both;
            }

CSS;
            $this->output.="</style>" . PHP_EOL;
            $this->output.="<form action='/install/step/2/' method='POST'>" . PHP_EOL;
            $i=0;
            foreach($sets as $key => $value){
                $i++;
                if(strpos($key,"_info")!=false) continue;
                if(is_array($value)){
                    $this->output.= "<fieldset id='setting_".$key."' class='settings'><legend>" . $key . "</legend>" . PHP_EOL;;
                    if(isset($sets[$key . "_info"])){
                        $this->output.="<div id='setting_info_".$key."'>" . $sets[$key . "_info"] . "</div>" . PHP_EOL;
                    }
                    
                    $this->output.= "<dl>" . PHP_EOL;
                    
                    foreach($value as $set=>$setValue){
                        if(isset($value[$set . "_info"])){
                            $comment="<div id='setting_info_".$key."_".$set."'>" . $value[$set . "_info"] . "</div>" . PHP_EOL;
                        } else {
                            $comment="";
                        }

                        if(strpos($set,"_info")!=false) continue;
                        if(!is_bool($setValue)) {
                            $this->output.="<dt>".$set . "</dt>" . PHP_EOL. "<dd>".$comment."<input name='".$key."_".$set."' type='text' value='" . $setValue . "' />". PHP_EOL;
                        }
                        else {
                            $this->output.="<dt>".$set . "</dt>" . PHP_EOL. "<dd>".$comment."<label><input name='".$key."_".$set."' type='radio'";
                            if($setValue == true){
                                $this->output.=" checked='checked'";
                            }
                            $this->output.=" value='true' />" . PHP_EOL. " Aan</label> <label><input name='".$key."_".$set."' type='radio'";
                            if($setValue == false){
                                $this->output.=" checked='checked'";
                            }
                            $this->output.=" value='false' /> Uit</label>". PHP_EOL;;  
                        }
                        $this->output.= "</dd>" . PHP_EOL;
                    }
                    
                    $this->output.="</dl>" . PHP_EOL. "</fieldset>" . PHP_EOL;
                }
            }
            
            $this->output.="<div class='clear'>" . PHP_EOL. "<input type='submit' value='Update' />" . PHP_EOL. "</div>" . PHP_EOL. "<form>";
            
            
            
            /*
            rename(basedir .'settings'. DS .'settings.json',basedir .'settings'. DS .'settings.json.old');
            $this->reg->settings->write_json_file($sets,basedir .'settings'. DS .'settings.json');
            */
    }
    
    public function checkTables(){
        
        $exists = false;
        $oldTables = $this->reg->database->query("show tables");
        $dbStruct = base64_decode(file_get_contents(basedir . "install" . DS . "databaseStructure"));
    
        $newTables = json_decode($dbStruct,true);
        foreach($newTables as $key=>$value){
            
            $tlist[$this->reg->database->prefixTable($key)] = true;
        }
        foreach($oldTables as $key=>$table){
            if(!is_numeric($key)) continue;
            if(isset($tlist[$table['Tables_in_project4']])){
                $exists = true;
                break;
            }
        }
        if($exists){
            $this->output = "We have detected a possible earlier install of skynet,<br /> please go back to step 1 and change your prefix.";
        } else {
            $this->output = "Start creating tables...<br />";
            $this->createTables();
        }
        
        
    }
    private function createTables(){
        $dbStruct = base64_decode(file_get_contents(basedir . "install" . DS . "databaseStructure"));
        $tables = json_decode($dbStruct,true);
        foreach($tables as $table=>$fields){
            
            $this->output .= $this->reg->database->prefixTable($table) . ": ";
            $succes = $this->reg->database->createTable($table,$fields,true);
            $this->output .= ($succes ? "Succeeded" : "Failed") . "<br />" . PHP_EOL;
            if(!$succes){
                $this->output .= $this->reg->database->lastError();
            }
        }
    }
    
    public function createAdminAccount($name,$email,$pass){
        $sql = $this->reg->database;//new Core_Database();
        $sql->insert("groups",array("Name"=>"Admin","Description"=>"Highest level account, has full access."));
        $sql->insert("users",array("Name"=>$name,"Email"=>$email,"Pass"=>sha1($pass)));
        $select = $sql->select("users","ID","WHERE Name='" . $name . "' AND Email='".$email."' AND Pass='".sha1($pass)."'");
        $uID = $select[0]['ID'];
        unset($select);
        $select = $sql->select("groups","ID","WHERE Name='Admin'");
        $gID = $select[0]['ID'];
        $sql->insert("groupmembers",array("uID"=>$uID,"gID"=>$gID));
    }
}

?>