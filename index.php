<?php

/**
 * Autoloader for functions, easy include
 * Using lazyman namespaces
*/
    function __autoload($className) {
        require_once "classes/" . strtolower(str_replace("_","/",$className)) . ".php";
    }
    
    new Core_Skynet()

?>