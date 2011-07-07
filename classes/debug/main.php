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
                    $msg = "CORE:\t(" . $ref.") " . $comment;
                }
                break;
            
            case "query":
                if($this->reg->settings->settings['debug']["query"] === true){
                    $msg = "QUERY:\t" . $comment;
                }
                break;
            
            case "error":
                if($this->reg->settings->settings['debug']["error"] === true){
                    $msg = "ERROR:\t(".$ref.") " . $comment;
                }

                break;
            
            case "notice":
            default:
                if($this->reg->settings->settings['debug']["notice"] === true){
                    $msg = "NOTICE:\t(".$ref.") " . $comment;
                }
                break;
        }
        $msg .= PHP_EOL;
        if($this->reg->settings->settings['debug']["log"] == true && isset($msg)){
            if(!file_exists(basedir . "logs")) {
                mkdir(basedir . "logs");
                chmod(basedir . "logs",0666);
            }
            $logFile = basedir . "logs" . DS . $name . "_" . date("d_m_y") . ".log";
            $fp = fopen($logFile,"a");
            fwrite($fp, $msg);
            fclose($fp);
            chmod($logFile,0666);    
        }
        if($this->reg->settings->settings['debug']["output"] == true && isset($msg)){
            $this->text .= $msg;
            $this->reg->view->assign("debugTxt",$this->text);
        }
    }
}

?>