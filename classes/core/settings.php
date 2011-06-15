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
     * @var Array contains all settings
    */
    public $settings;
    
    /**
     * __construct
     * Constructor, initialize class, define base_url, load settings from settings.ini
     * and adds itself to registery
    */
    
    function __construct()
    {
        $this->reg = Core_Registery::singleton();
        $this->reg->settings = $this;
        
        //Deze functie eigenlijk vanaf de index.php aanroepen. Voor developing nu even zo.
        $this->setIni(basedir .'settings'. DS .'settings.ini');
    }
    
    public function setIni($file) 
    {	
        #check if file exists
        if(!is_readable($file) == true)
        {
                throw new Exception('Ini file not found ' . $file);
        }
        
        #set ini vars in class
        $this->settings = parse_ini_file($file, true);
    }
}

?>