<?php
/**
 * index.php
 * Main index file
 * @version 0.1
 * @author Thom Werring <info@werringweb.nl> & Lucas Weijers <meel_to_lucas@hotmail.com>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet
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
    
    
    //create skynet website
    $skynet = new Core_Skynet();
    $skynet->main();