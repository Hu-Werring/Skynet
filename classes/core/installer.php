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
    
    
    /*
     * __construct()
     */
    
    function __construct() {
        $this->reg = Core_Registery::singleton();
        
    }
    
    public function createTables(){
        $dbStruct = base64_decode(file_get_contents(basedir . "install" . DS . "databaseStructure"));
    
        $tables = json_decode($dbStruct,true);
    
        $output = "";
        foreach($tables as $table=>$fields){
            
            $output .= $reg->database->prefixTable($table) . ": ";
            $succes = $reg->database->createTable($table,$fields,true);
            $output .= var_export($succes,true) . PHP_EOL;
            if(!$succes){
                $output .= $reg->database->lastError();
            }
        }
    }
}

?>