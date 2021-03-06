<?php
/**
 * view.php
 * @package Skynet_core
*/
/**
 * Class Core_View
 * @version 0.1
 * @author Lucas Weijers <meel_to_lucas@hotmail.com>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_core
*/

class Core_View
{
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg = null;
    
    /**
     * $template
     * contains the templates filename
     * @access private
     * @var string filename
     *
     */
    
    /** $templatePath
     * contains the path to te directory that contains the templates
     * @access private
     * @var string templatepath
    */
    private $template;
    private $templatePath;
    
    /**
     *$tpl
     *Link to the template engine
     *@access public
     */
    public $tpl;
    
    /**
     *$css_files
     *array that contains the css files.
     *@access public
     *@var array contains css filenames.
     */
    /**
     *$js_files
     *array that contains the js files
     *@access public
     *@var array contains js filenames
     */
    public $css_files = array();
    public $js_files  = array();
        
    /**
     * __construct
     * Constructor, initialize class, define template var, load settings from settings.ini
     * and adds itself to registery
    */

    function __construct()
    {
        $this->reg = Core_Registery::singleton();
        $this->reg->view = $this;
        $this->initTemplateEngine();
        
    }
    
    /**
     * initTemplateEngine
     * Initializes the template engine rainTPL
     * @access private
    */
    private function initTemplateEngine()
    {
        include_once basedir.'classes/lib/raintpl.php';
        //raintpl::$tpl_dir = "view/"; // template directory
        //raintpl::$cache_dir = "tmp/"; // cache directory
        $this->tpl = new RainTPL();
        $this->tpl->configure('tpl_dir', basedir.'view/');
        $this->tpl->configure('cache_dir', basedir.'tmp/');
    }
    
    /**
     * setTemplatePath
     * Set the templatePath for the templates.
     * @access public
     * @param string templatepath
     */
    public function setTemplatePath($string)
    {
        $this->templatePath = $string;
        $this->tpl->configure('tpl_dir', basedir.$string);
    }
    
    /**
     * __get
     * Get variables out raintpl
     * @access public
     * @param String $var Variable to get out of rainTPL
     * @return Mixed data from rainTPL
    */
    public function __get($var)
    {
        return (isset($this->tpl->$var) ? $this->tpl->$var : null);
    }
    
    /**
     * __call
     * Call functions from rainTPL
     * @access public
     * @param String $functionName name of rainTPL function
     * @param Array Arguments passed to function
     * @return Mixed Data from rainTPL function or false
    */
    public function __call($functionName, $args)
    {
        if(method_exists($this->tpl, $functionName))  
        {
                return call_user_func_array(array($this->tpl, $functionName),$args);
        }
        else
        {
            return false;
        }
    }

    /**
     * add_css
     * add css file to be included with the template
     * @access public
     * @param $filename The filename of the css file.
     */
    
    public function add_css ( $filename )
    {

        
        if(substr($filename,0,1) != "/"){
            $path = basedir . $this->templatePath . 'css' . DS . $filename;
            $fileLoc = DS . $this->templatePath  . 'css' . DS . $filename;
        } else {
            $path = basedir . $filename;
            $fileLoc = $filename;
        }
        if( in_array( $fileLoc, $this->css_files ) == true )
        {
                return null;
        }
        
        if( file_exists($path) == false )
        {
                /*------------ERROR------------*/
              //  $this->reg->debug->error('Error','Function add_css()', 'Css file not found. File: '.$path, DateTime, $this);
                
                //log::write("Css file not found. File: `$path`", $this, 'add_css()');
                return false;
        }
        $this->css_files[] = str_replace("\\","/", $fileLoc);
        $this->includeLibs();
    }

    /**
     * add_js
     * add js file to be included with the template
     * @access public
     * @param $filename The filename of the js file.
     */
    public function add_js ( $filename )
    {
        
        //$path =  basedir . $this->templatePath . 'css' . DS . $filename;

        if(substr($filename,0,1) != "/"){
            $path = basedir . $this->templatePath . 'js' . DS . $filename;
            $fileLoc = DS . $this->templatePath . 'js' . DS . $filename;
        } else {
            $path = basedir . $filename;
            $fileLoc = $filename;
        }
        if( in_array( $fileLoc, $this->css_files ) == true )
        {
                return null;
        }
        
        if( file_exists($path) == false )
        {
                //log::write("Js file not found. File: `$path`", $this, 'add_js()');
                return false;
        }
        
        $this->js_files[] = str_replace("\\","/", $fileLoc);
        $this->includeLibs();
    }
    
    /**
     * includeLibs
     * include the css and js scripts / libraries in the template.
     * @access public
     */
    public function includeLibs()
    {
            $string = '';

            if($this->css_files)
            {
                    foreach( $this->css_files as &$css_file )
                    {
                        $string .=  '<link rel="stylesheet" href="'  . $css_file . '" type="text/css" />';
                    }
            }
            
            if($this->js_files)
            {
                    foreach( $this->js_files as &$js_file )
                    {
                            $string .= PHP_EOL . '<script src="' . $js_file . '" type="text/javascript"></script>';
                    }
            }
            $this->tpl->assign("libs", $string . PHP_EOL);
    }
}

?>