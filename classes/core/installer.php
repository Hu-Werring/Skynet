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
    
    private $output = "";
    /*
     * __construct()
     */
    
    function __construct() {
        $this->reg = Core_Registery::singleton();
        
        //$this->checkTables();
        //$this->createTables();
        $this->showSettings();
        echo nl2br($this->output);
    }
    
    private function showSettings(){
            $sets = $this->reg->settings->settings;
            $this->output.="<style>";
            $this->output.=<<<CSS
            .settings {    width: 30%;    float: left;} .clear{clear:both;}
CSS;
            $this->output.="</style>";
            $this->output.="<form action='/install/' method='POST'>";
            $i=0;
            foreach($sets as $key => $value){
                $i++;
                if(is_array($value)){
                    $this->output.= "<fieldset id='setting_".$i."' class='settings'><legend>" . $key . "</legend><dl>";
                    
                    foreach($value as $set=>$setValue){
                        if(!is_bool($setValue)) {
                            $this->output.="<dt>".$set . "</dt> <dd><input name='".$key."_".$set."' type='text' value='" . $setValue . "' /></dd>". PHP_EOL;
                        }
                        else {
                            $this->output.="<dt>".$set . "</dt> <dd><label><input name='".$key."_".$set."' type='radio'";
                            if($setValue == true){
                                $this->output.=" checked='checked'";
                            }
                            $this->output.=" value='true' /> Aan</label> <label><input name='".$key."_".$set."' type='radio'";
                            if($setValue == false){
                                $this->output.=" checked='checked'";
                            }
                            $this->output.=" value='false' /> Uit</label></dd>". PHP_EOL;;
                            
                            
                           
                        }
                    }
                    
                    $this->output.="</dl></fieldset>";
                }
            }
            
            $this->output.="<div class='clear'><input type='submit' value='Update' /></div><form>";
            
            
            
            /*$sets['db']['prefix']=uniqid() . "_";
            rename(basedir .'settings'. DS .'settings.json',basedir .'settings'. DS .'settings.json.old');
            $this->reg->settings->write_json_file($sets,basedir .'settings'. DS .'settings.json');
            */
    }
    
    private function checkTables(){
        
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
            $this->output = "We have detected a possible earlier install of skynet, please change the settings.ini file";
        } else {
            $this->output = "Checked your database, install is possible.";
        }
        
        
    }
    private function createTables(){
        $dbStruct = base64_decode(file_get_contents(basedir . "install" . DS . "databaseStructure"));
    
        $tables = json_decode($dbStruct,true);
    
        $this->output = "";
        foreach($tables as $table=>$fields){
            
            $this->output .= $this->reg->database->prefixTable($table) . ": ";
            $succes = $this->reg->database->createTable($table,$fields,true);
            $this->output .= var_export($succes,true) . PHP_EOL;
            if(!$succes){
                $this->output .= $this->reg->database->lastError();
            }
        }
}
}

?>