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
    
    public function write_php_ini($array, $file)
    {
        $res = array();
        foreach($array as $key => $val)
        {
            if(is_array($val))
            {
                $res[] = "[$key]";
                foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
            }
            else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
        }
        $this->safefilerewrite($file, implode("\r\n", $res));
    }

    private function safefilerewrite($fileName, $dataToSave)
    {    if ($fp = fopen($fileName, 'w'))
        {
            $startTime = microtime();
            do
            {            $canWrite = flock($fp, LOCK_EX);
               // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
               if(!$canWrite) usleep(round(rand(0, 100)*1000));
            } while ((!$canWrite)and((microtime()-$startTime) < 1000));
    
            //file was locked so now we can store information
            if ($canWrite)
            {            fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    
    }
    
}

?>