<?php
/**
 * index.php
 * Installer index file
 * @version 0.1
 * @author Thom Werring <info@werringweb.nl> & Lucas Weijers <meel_to_lucas@hotmail.com>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
*/


    /**
     * DS
     * Shortcut for DIRECTORY_SEPARATOR
    */
    define("DS",DIRECTORY_SEPARATOR);
    /**
     * basedir
     * Absolute path to ducument root
    */
    define("basedir",$_SERVER['DOCUMENT_ROOT'] . DS);


    /**
     * __autoload
     * Autoloader for functions, easy include
     * Using lazyman namespaces
    */    
    function __autoload($className) {
        require_once basedir . "classes/" . strtolower(str_replace("_","/",$className)) . ".php";
    }
    
    
    //Create Skynet Installer
    $skynet = new Core_Skynet(false);
    $skynet->install();
