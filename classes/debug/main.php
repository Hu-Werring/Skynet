<?php

/*
 * class Debug_Main
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
    
    private function dump($data){
        var_dump($data);
    }
    public function __call($name,$args){
        if($this->allowDebug)
            return @call_user_func_array(array($this, $name),$args);
        else
            @file_put_contents(date("d_m_y") . ".log","DEBUG:" . $name . " called with " . var_export($args,true));
        return false;
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    /**
     * msg
     * Takes care off all notices, messages, errors, warnings, fatals, querys etc
     * @access public
     * @param $type
     * @param $Name the name of the msg
     * @param $comment info or comment on a msg
     * @param $ref the reference to te script that called this function
     */
    
    //Types: core, query, error, notice. More?thom?
    public function msg($type, $Name, $comment, $ref)
    {
       /* switch($type)
        {
            case core:
                //
                break;
            
            case query:
                //
                break;
            
            case error:
                //
                break;
            
            case notice:
            default:
                //
                break;
        }*/
    }
}

?>