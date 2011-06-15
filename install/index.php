<?php

 define("DS",DIRECTORY_SEPARATOR);
    define("basedir",$_SERVER['DOCUMENT_ROOT'] . DS);
    /**
     * Autoloader for functions, easy include
     * Using lazyman namespaces
    */  
    function __autoload($className) {
        require_once basedir . "classes/" . strtolower(str_replace("_","/",$className)) . ".php";
    }
    
    
    
    $skynet = new Core_Skynet();
    $skynet->install();
    $reg = Core_Registery::singleton();
    
    
    

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
    
    //echo '$tables["' . $table . '"] = ' . var_export($fields,true) . ";" . PHP_EOL . PHP_EOL;
    }
    /**/
    echo nl2br($output);