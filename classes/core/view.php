<?php
/*
 * Class Core_View
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
     * $templatePath
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
        $this->tpl->configure('tpl_dir', 'view/');
        $this->tpl->configure('cache_dir', 'tmp/');
    }
    
    /**
     * setTemplate
     * Set the template for the current view.
     * @access public
     * @param string templatename
     */
    public function setTemplate($string)
    {
        $template = $string;
    }
    
    /**
     * setTemplatePath
     * Set the templatePath for the templates.
     * @access public
     * @param string templatepath
     */
    public function setTemplatePath($string)
    {
        $templatePath = $string;
    }
}

?>