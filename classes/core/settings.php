<?php
/*
 * Class Core_Settings
 */

class Core_Settings
{
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg = null;
    
    /**
     * $settings
     * Contains the loaded settings
     * @access public
     * @staticvar static
    */
    public static $settings;
    
    /**
     * __construct
     * Constructor, initialize class, define base_url, load settings from settings.ini
     * and adds itself to registery
    */
    
    function __construct()
    {
        $this->reg = Core_Registery::singleton();
        $this->reg->settings = $this;
        
        /**
         * Define base_url
         * @global base_url
        */
        //define(base_url, __FILE__);
       
        //Deze functie eigenlijk vanaf de index.php aanroepen. Voor developing nu even zo.
        $this->setIni('../../settings/settings.ini');
    }
    
    public function setIni($file) 
    {	
        #check if file exists
        if(!is_readable($file) == true)
        {
                throw new Exception('Ini file not found ' . $file);
        }
        else
        {
            echo '';
        }
        
        #set ini vars in class
        $this->settings = parse_ini_file($file, true);
    }
}

?>