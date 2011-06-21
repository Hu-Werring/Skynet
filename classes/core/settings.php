<?php
/**
 * settings.php
 * @package Skynet
*/
/**
 * Class Core_Settings
 * Handles settings
 * @version 0.5
 * @author Thom Werring <info@werringweb.nl>
 * @author Lucas Weijers <meel_to_lucas@hotmail.com>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet
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
        $this->setJson(basedir .'settings'. DS .'settings.json');
    }
    
    /**
     * setJson
     * check if our json settings file exists
     * if not, be doomed.
     * else loadn and decode it to $this->settings
     * @access Public
     * @param String $file Path to json file
    */
    public function setJson($file) 
    {	
        #check if file exists
        if(!is_readable($file) == true)
        {
                throw new Exception('json file not found ' . $file);
        }
        
        #set json vars in class
        $this->settings = json_decode(file_get_contents($file), true);
    }
    
    /**
     * write_json_file
     * pretty write new json
     * @access public
     * @param Array $array Array containing new settings file
     * @param String $file path to file to be written
    */
    public function write_json_file($array, $file)
    {
        $json = str_replace('{','{' . PHP_EOL,json_encode($array));
        $json = str_replace('}',PHP_EOL. '}',$json);
        $json = str_replace(',',','.PHP_EOL,$json);
        
        $eJson = explode(PHP_EOL,$json);
        
        $indent = 0;
        for($i=0;$i<count($eJson);$i++){
            trim($eJson[$i]);
            if(strpos($eJson[$i],"}")!==false){
                $indent--;
            }
            $eJson[$i] = str_repeat("\t",$indent) . $eJson[$i];
            if(strpos($eJson[$i],"{")!==false){
                $indent++;
            }
        
        }
        $json = implode(PHP_EOL,$eJson);

        $this->safefilerewrite($file, $json);
    }

    /**
     * safefilerewrite
     * checks if file is writeable and writes data to it.
     * @access private
     * @param String $fileName Name of file to be written to
     * @param String $dataToSave Data to be written
    */
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