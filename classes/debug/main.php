<?php

/*
 * class Debug_Main
 */

class Debug_Main  {
    
    private $reg;
    
    /**
     * __construct
     * creates debug class.
    */
    function __construct() {
        $this->reg = Core_Registery::singleton();
        $this->reg->debug = $this;
    }
    
    /**
     * print_pre
     * This function prints raw data with pre tags for readability.
     * @access public
     * @param mixed $data
     */
    
    public function print_pre($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

?>