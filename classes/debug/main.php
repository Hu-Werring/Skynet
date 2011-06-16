<?php

/*
 * class Debug_Main
 */

class Debug_Main  {
    
    
    private $allowDebug = false;
    
    private $reg;
    
    
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
}

?>