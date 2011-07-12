<?php
/**
 * installer.php
 * @package Skynet_core
*/

/**
 * class Core_Installer
 * Installer core class
 * @version 0.5
 * @author Thom Werring <info@werringweb.nl>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_core
*/

class Core_Installer {
    
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg = null;
    
    
    /**
     * Stores Output to send to template
     * @access public
    */
    public $output = "";
    
    /**
     * $disAllowNextStep
     * @access private
     * @see Core_Installer::nextStep()
    */
    private $disAllowNextStep=false;
    
    /*
     * __construct()
     * adds Core_Installer to registery
     */
    
    function __construct() {
        $this->reg = Core_Registery::singleton();
        
        $this->reg->installer = $this;
    }
    
    /**
     * showSettings
     * Sends settings in settings.json to $output in nice HTML form
     * @access public
    */
    public function showSettings(){
        $this->reg->debug->msg("NOTICE","INSTALL","Display settings from settings.json",__CLASS__ . ":" . __LINE__);
            $sets = $this->reg->settings->settings;
            $this->output.="<form action='/install/step/2/' method='POST'>" . PHP_EOL;
            $i=0;
            foreach($sets as $key => $value){
                $i++;
                if(strpos($key,"_info")!=false) continue;
                if(is_array($value)){
                    $this->output.= "<fieldset id='setting_".$key."' class='settings'><legend>Step 1 - " . $key . "</legend><div class='holder'>" . PHP_EOL;;
                    if(isset($sets[$key . "_info"])){
                        $this->output.="<div class='setting_info' id='setting_info_".$key."'>" . $sets[$key . "_info"] . "</div>" . PHP_EOL;
                    }
                    
                    $this->output.= "<dl>" . PHP_EOL;
                    
                    foreach($value as $set=>$setValue){
                        if(isset($value[$set . "_info"])){
                            $comment="<div id='setting_info_".$key."_".$set."'>" . $value[$set . "_info"] . "</div>" . PHP_EOL;
                        } else {
                            $comment="";
                        }

                        if(strpos($set,"_info")!=false) continue;
                        if(!is_bool($setValue)) {
                            $this->output.="<dt>".$set . "</dt>" . PHP_EOL. "<dd>".$comment."<input name='".$key."_".$set."' type='text' value='" . $setValue . "' />". PHP_EOL;
                        }
                        else {
                            $this->output.="<dt>".$set . "</dt>" . PHP_EOL. "<dd>".$comment."<label><input name='".$key."_".$set."' type='radio'";
                            if($setValue == true){
                                $this->output.=" checked='checked'";
                            }
                            $this->output.=" value='true' />" . PHP_EOL. " Enable</label> <label><input name='".$key."_".$set."' type='radio'";
                            if($setValue == false){
                                $this->output.=" checked='checked'";
                            }
                            $this->output.=" value='false' /> Disable</label>". PHP_EOL;;  
                        }
                        $this->output.= "<span class='help'></span></dd>" . PHP_EOL;
                    }
                    
                    $this->output.="</dl>" . PHP_EOL;
                    if($i == 1){
                        $this->output.="<p id='advancedHolder' style='text-align: right; padding-right: 5px;'><label><input type='checkbox' id='goAdvanced'>Display developer options</label></p>";
                    }
                    $this->output.="</div></fieldset>" . PHP_EOL;
                }
            }
            
            $this->output.="<div class='clear' id='submitButton'>" . PHP_EOL. "<input type='submit' value='Update' />" . PHP_EOL. "</div>" . PHP_EOL. "<form>";
            
            
            
    }
    
    /**
     * checkTables
     * see if there is already a table that we are about to install, if not, install the tables
     * @param boolean $force force install even if we have to override existing tables
     * @see Core_Installer::createTables()
     * @access private
    */
    public function checkTables($force=false){
        $this->reg->debug->msg("NOTICE","INSTALL","Checking for earlier install of skynet",__CLASS__ . ":" . __LINE__);
        //Init Core_Database, it won't autostart in the installer due to step 1
        new Core_Database();
        $exists = false;
        $oldTables = $this->reg->database->query("show tables");
        $dbStruct = base64_decode(file_get_contents(basedir . "install" . DS . "databaseStructure"));
    
        $newTables = json_decode($dbStruct,true);
        foreach($newTables as $key=>$value){
            
            $tlist[$this->reg->database->prefixTable($key)] = true;
        }
        foreach($oldTables as $key=>$table){
            if(!is_numeric($key)) continue;
            $name = $this->reg->settings->settings['db']['name'];
            

            
            if(isset($tlist[$table['Tables_in_' . $name ]])){
                $exists = true;
                break;
            }
        }
        if($exists && !$force){
            $this->reg->debug->msg("NOTICE","INSTALL","Old skynet install has been found",__CLASS__ . ":" . __LINE__);
            $this->output = "We have detected a possible earlier install of skynet,<br /> please go back to step 1 and change your prefix.<br/>Or check the box to override your old Skynet install.<br/><strong>All data will be lost if you override the old system.</strong>";
            $this->disAllowNextStep=true;
        } else {
            if($force){
                $this->reg->debug->msg("NOTICE","INSTALL","Old skynet install has been found, but force new install",__CLASS__ . ":" . __LINE__);
            }
            $this->output = "Start creating tables...<br />";
            $this->createTables();
        }
        $this->output.="</fieldset><div class='clear' id='submitButton'>";
            
        
    }
    
    /**
     * createTables
     * create all tables needed for base install of CMS
     * @access private
    */
    private function createTables(){
        $this->reg->debug->msg("NOTICE","INSTALL","Installing tables",__CLASS__ . ":" . __LINE__);
        $dbStruct = base64_decode(file_get_contents(basedir . "install" . DS . "databaseStructure"));
        $tables = json_decode($dbStruct,true);
        foreach($tables as $table=>$fields){
            
            $this->output .= $this->reg->database->prefixTable($table) . ": ";
            $succes = $this->reg->database->createTable($table,$fields,true);
            $this->output .= ($succes ? "Succeeded" : "Failed") . "<br />" . PHP_EOL;
            if(!$succes){
                $this->reg->debug->msg("NOTICE","INSTALL","Error: " . $this->reg->database->lastError(),__CLASS__ . ":" . __LINE__);
                $this->output .= $this->reg->database->lastError();
            }
        }
        
        
        //insert default data
        $this->reg->debug->msg("NOTICE","INSTALL","installing default setup",__CLASS__ . ":" . __LINE__);
        $this->reg->database->insert("templates",array("Naam"=>"Default","Description"=>"The standard Skynet Template","Locatie"=>"home"));
        $this->reg->database->insert("categorieen",array("Naam"=>"Default","Description"=>"The standard Skynet Category"));
        $this->reg->database->insert("artikelen",array("cID"=>"1","Content"=>"Welcome to SkyNet","LastUpdate"=>time(),"Titel"=>"Welcome"));
        $this->reg->database->insert("pages",array("tID"=>"1","Zichtbaar"=>"1","Positie"=>"0","Naam"=>"home"));
        $this->reg->database->insert("pagecontent",array("aID"=>"1","pID"=>"1","type"=>"1"));
        

    }
    
    
    /**
     * createAdminAccount
     * create first admin account and runs first install insterts
     * password will be sha1 encoded before its send to the database
     * @access public
     * @param String $name name of admin account
     * @param String $email email of admin account
     * @param String $pass unencoded password for admin account
    */
    public function createAdminAccount($name,$email,$pass){
        $this->reg->debug->msg("NOTICE","INSTALL","Creating first admin account (".$name.")",__CLASS__ . ":" . __LINE__);
        if(!isset($this->reg->database)){
            $sql = new Core_Database();
        } else {
            $sql = $this->reg->database;
        }

        $sql->insert("groups",array("Name"=>"Admin","Description"=>"Highest level account, has full access."));
        $sql->insert("users",array("Name"=>$name,"Email"=>$email,"Pass"=>sha1($pass)));
        $select = $sql->select("users","ID","WHERE Name='" . $name . "' AND Email='".$email."' AND Pass='".sha1($pass)."'");
        $uID = $select[0]['ID'];
        unset($select);
        $select = $sql->select("groups","ID","WHERE Name='Admin'");
        $gID = $select[0]['ID'];
        $sql->insert("groupmembers",array("uID"=>$uID,"gID"=>$gID));
    }
    
    /**
     * createAdminAccountForm
     * sends a form for the creation of first admin account to $output
     * @access public
    */
    public function createAdminAccountForm(){
        $this->reg->debug->msg("NOTICE","INSTALL","Create new Admin account",__CLASS__ . ":" . __LINE__);
        if(!isset($this->reg->database)){
            $sql = new Core_Database();
        } else {
            $sql = $this->reg->database;
        }
        
        
        $output = <<<HTML
        <form action='/install/step/5/' method='POST'>
            <fieldset class='step'>
                <legend>Step 4 - Create Account</legend>
                    <dl>
                        <dt>Name</dt>
                        <dd><input id='adminName' type='text' name='name' /><br/><span id='adminNameInfo' class='createInfo'>A name should be at least 4 characters.</span></dd>
                        <dt>Email</dt>
                        <dd><input id='adminEmail' type='text' name='email' /><br/><span id='adminEmailInfo' class='createInfo'>The email has to be valid.</span></dd>
                        <dt>Email - repeat</dt>
                        <dd><input id='adminEmailCheck' type='text' name='emailCheck' /><br/><span id='adminEmailCheckInfo' class='createInfo'>The email should match the previous field.</span></dd>
                        <dt>Password</dt>
                        <dd><input id='adminPass' type='password' name='pass' /><br/><span id='adminPassInfo' class='createInfo'>A password should be at least 8 characters long</span></dd>
                        <dt>Password - repeat</dt>
                        <dd><input id='adminPassCheck' type='password' name='passCheck' /><br/><span id='adminPassCheckInfo' class='createInfo'>The password should match the previous field.</span></dd>
                    </dl>
                      
            </fieldset>
            <div class='clear' id='submitButton'><input type="submit" value='Next step' /></div>
        </form>
HTML;
    $this->output = $output;
    }
    
    /**
     * nextStep
     * creates a "next step" button for non-interactive pages
     * Or a form to "try again" + "back to step 1" based on Core_Installer::$disallowNextStep
     * @access public
     * @param String $step Nummeric string for next step
    */
    public function nextStep($step){
        if(!$this->disAllowNextStep){
        $this->output .= <<<HTML
<form id='nextStep' action="/install/step/$step/" method="POST">
<input type="submit" value='Next step' /> 
</form>
HTML;
        } else {
            $currentStep = $step -1;
            $this->output .= <<<HTML
            <form action="/install/step/$currentStep/" method='POST' style='display: inline;'>
            <input type='checkbox' name="force" id='override' value='true' />Check to override old values
            <input type="submit" value='Try Again' />
            </form><form action="/install/step/1/" method="POST" style='display: inline;'>
<input type="submit" value='Back to step 1' />
</form>
HTML;
        }
    }
}

?>