<?php
    define("basedir","./");
    /**
     * Autoloader for functions, easy include
     * Using lazyman namespaces
    */  
    function __autoload($className) {
        require_once basedir . "classes/" . strtolower(str_replace("_","/",$className)) . ".php";
    }
    
    
    //create skynet website
    $skynet = new Core_Skynet();
    $skynet->main();
    
?>
