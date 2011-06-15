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
        
        /**
         * Define base_url
         * @global base_url
        */
        //define(base_url, __FILE__);
        
        /**
         * Define DS
         * Defining the directory seperator
         */
        define('DS', DIRECTORY_SEPARATOR);
        
        //Deze functie eigenlijk vanaf de index.php aanroepen. Voor developing nu even zo.
        $this->setIni('settings'. DS .'settings.ini');
        if($this->settings['system']['modus'] === 'debug' || ($_SERVER['REMOTE_ADDR'] && $this->settings['system']['modus'] === 'localdebug'))
        {
            new Debug_Main();
        }
        $this->reg->debug->print_pre($this->settings);
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