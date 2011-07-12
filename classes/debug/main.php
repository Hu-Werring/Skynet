<?php
/**
 * main.php
 * @package Skynet_debug
*/
/**
 * class Debug_Main
 * @version 0.1
 * @author Thom Werring <info@werringweb.nl>
 * @author Lucas Weijers <meel_to_lucas@hotmail.com>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_debug
*/

class Debug_Main  {
    
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg;
    
    /**
     * $allowDebug
     * Setting that can be used to check if debugging is enabled
     * @access private
    */
    private $allowDebug = false;
    
    public $text = "";
    /**
     * __construct
     * creates debug class.
    */
    function __construct() {
        $this->reg = Core_Registery::singleton();
        $this->reg->debug = $this;
        
        if($this->reg->settings->settings['system']['modus'] === 'debug' || ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' && $this->reg->settings->settings['system']['modus'] === 'localdebug'))
        {
            $this->allowDebug = true;
            error_reporting(E_ALL | E_STRICT);
        } else {
            error_reporting(E_ALL ^ E_NOTICE);
        }
    }
    
    /**
     * print_pre
     * This function prints raw data with pre tags for readability.
     * @access public
     * @param mixed $data
     */
    
    private function print_pre($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
    /**
     * dump
     * vardumps data if we are allowed to.
     * @access private
     * @param Mixed $data Data to be vardumped
    */
    private function dump($data){
        var_dump($data);
    }
    
    /**
     * __call
     * magic caller to functions in this class if its allowed
     * else write to log
     * @todo fix log writing
     * @param String $name name of function
     * @param Array $args Arguments of function
     * @return Mixed result of function or false
    */
    public function __call($name,$args){
        if($this->allowDebug)
            return @call_user_func_array(array($this, $name),$args);
        else
            $this->msg("DEBUG","",$name . " called with " . var_export($args,true));
        return false;
    }
    
    /**
     * __get
     * @access public
    */
    public function __get($name)
    {
        return $this->$name;
    }
    
    /**
     * msg
     * Takes care off all notices, messages, errors, warnings, fatals, querys etc
     * @access public
     * @param $type
     * @param $name the name of the msg
     * @param $comment info or comment on a msg
     * @param $ref the reference to te script that called this function (should be __CLASS__:__LINE__)
     */
    
    public function msg($type, $name, $comment, $ref="")
    {
        $comment = var_export($comment,true);
        switch($type)
        {
            case "core":
                if($this->reg->settings->settings['debug']["core"] === true){
                    $msg = "CORE:\t(" . $ref.")\t" . $comment;
                }
                break;
            
            case "query":
                if($this->reg->settings->settings['debug']["query"] === true){
                    $msg = "QUERY:\t" . $comment;
                }
                break;
            
            case "error":
                if($this->reg->settings->settings['debug']["error"] === true){
                    $msg = "ERROR:\t(".$ref.")\t" . $comment;
                }

                break;
            
            case "notice":
            default:
                if($this->reg->settings->settings['debug']["notice"] === true){
                    $msg = "NOTICE:\t(".$ref.")\t" . $comment;
                }
                break;
        }
        
        if($this->reg->settings->settings['debug']["log"] == true && isset($msg)){
        $msg .= PHP_EOL;
        $msg = date("H:i:s") . "\t" . $msg;
        if(!file_exists(basedir . "logs")) {
                mkdir(basedir . "logs");
                chmod(basedir . "logs",0755);
            }
            $logFile = basedir . "logs" . DS . date("Y_m_d") . "_".strtolower($name).".log";
            $fp = fopen($logFile,"a");
            fwrite($fp, $msg);
            fclose($fp);
            chmod($logFile,0666);    
        }
        if($this->reg->settings->settings['debug']["output"] == true && isset($msg)){
            $this->text .= $msg . PHP_EOL;
            $this->reg->view->assign("debugTxt",$this->text);
        }
    }
    
    
    public function validatePHP($fileName, $checkIncludes = true)
    {
        $return = true;
        // If it is not a file or we can't read it throw an exception
        if(!is_file($fileName) || !is_readable($fileName))
            return false;
       
        // Sort out the formatting of the filename
        $fileName = realpath($fileName);
       
        // Get the shell output from the syntax check command
        $output = shell_exec('php -l "'.$fileName.'"');
       
        // Try to find the parse error text and chop it off
        $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);
       
        // If the error text above was matched, throw an exception containing the syntax error
        if($count > 0)
            return false;
       
        // If we are going to check the files includes
        if($checkIncludes)
        {
            foreach($this->getIncludes($fileName) as $include)
            {
                // Check the syntax for each include
                $return = $this->validatePHP($include);
                if(!$return) break;
            }
        }
        return $return;
    }
   
    private function getIncludes($fileName)
    {
        // NOTE that any file coming into this function has already passed the syntax check, so
        // we can assume things like proper line terminations
           
        $includes = array();
        // Get the directory name of the file so we can prepend it to relative paths
        $dir = dirname($fileName);
       
        // Split the contents of $fileName about requires and includes
        // We need to slice off the first element since that is the text up to the first include/require
        $requireSplit = array_slice(preg_split('/require|include/i', file_get_contents($fileName)), 1);
       
        // For each match
        foreach($requireSplit as $string)
        {
            // Substring up to the end of the first line, i.e. the line that the require is on
            $string = substr($string, 0, strpos($string, ";"));
           
            // If the line contains a reference to a variable, then we cannot analyse it
            // so skip this iteration
            if(strpos($string, "$") !== false)
                continue;
           
            // Split the string about single and double quotes
            $quoteSplit = preg_split('/[\'"]/', $string);
           
            // The value of the include is the second element of the array
            // Putting this in an if statement enforces the presence of '' or "" somewhere in the include
            // includes with any kind of run-time variable in have been excluded earlier
            // this just leaves includes with constants in, which we can't do much about
            if($include = $quoteSplit[1])
            {
                // If the path is not absolute, add the dir and separator
                // Then call realpath to chop out extra separators
                if(strpos($include, ':') === FALSE)
                    $include = realpath($dir.DIRECTORY_SEPARATOR.$include);
           
                array_push($includes, $include);
            }
        }
       
        return $includes;
    }
}

?>