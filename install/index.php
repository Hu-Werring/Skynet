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
    
?>