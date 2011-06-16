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
    
    private $output;
    /*
     * __construct()
     */
    
    function __construct() {
        $this->reg = Core_Registery::singleton();
        $this->checkTables();
        //$this->createTables();
        
        echo nl2br($this->output);
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
            if($tlist[$table['Tables_in_project4']] === true){
                $exists = true;
                break;
            }
        }
        if($exists){
            $this->output = "We have detected a possible earlier install of skynet, do you want to override these tables?";
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