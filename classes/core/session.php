<?php
/**
 * session.php
 * @package Skynet_Core
*/
/**
 * Class Core_Session
 * 
 * @version 0.1
 * @author Thom Werring <info@werringweb.nl>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_Core
*/

class Core_Session
{
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg = null;
    
    private $loggedIn = null;
        
    /**
     * __construct
     * Constructor, initialize class, define base_url, load settings from settings.ini
     * and adds itself to registery
    */
    
    function __construct()
    {
        $this->reg = Core_Registery::singleton();
        $this->reg->session = $this;
        
        $this->clearUnvalid();
    }
    
    public function create($name,$pass){
        $table = "users";
        $select = "*";
        $sname = $this->reg->database->res($name);
        $spass = $this->reg->database->res($pass);
        $where = "Where Name = '" . $sname . "' AND Pass = '".$spass."'";
        $result = $this->reg->database->select($table,$select,$where);
        if($result['affected'] == 1){
            $time = time()+ 60*60*24*365;
            $sessionHash = md5($sname . "|" . $spass . "|" . $time);
            $sessionData = array();
            $sessionData['uID'] = $result[0]["ID"];
            $sessionData['sHash'] = $sessionHash;
            $sessionData['ValidTill'] = $time;
            
            setcookie("skynet_loginsession",json_encode($sessionData),$time,"/");
            $table = $this->reg->database->prefixTable("sessions");
            $qry = "INSERT into $table (uID,sHash,ValidTill) VALUES('$sessionData[uID]','$sessionData[sHash]','$sessionData[ValidTill]') ON DUPLICATE KEY UPDATE sHash='$sessionData[sHash]', ValidTill='$sessionData[ValidTill]'";
            $this->reg->database->qry($qry);
            return true;
        }
        else return false;
    }
    
    public function checkCurrent(){
        if(!is_null($this->loggedIn)){
            return $this->loggedIn;
        }
        if(isset($_COOKIE['skynet_loginsession'])){
            $sessionData = json_decode($_COOKIE['skynet_loginsession'],true);
            
            $uID = $this->reg->database->res($sessionData['uID']);
            $userT = $this->reg->database->prefixTable("users");
            $sessionT = $this->reg->database->prefixTable("sessions");
            $qry = <<<SQL
SELECT $sessionT.sHash FROM $userT
inner join $sessionT
on $sessionT.uID = $userT.ID
WHERE $userT.ID = '$uID'
SQL;
            $result = $this->reg->database->qry($qry);
            
            if($result['affected'] == 1){
                if($result[0]['sHash'] == $sessionData['sHash']){
                    $this->loggedIn = true;
                    return true;
                }
            } 
        }
        $this->loggedIn = false;
        return false;
    }
    
    private function clearUnvalid(){
        $table = $this->reg->database->prefixTable('sessions');
        $qry = "DELETE FROM $table WHERE ValidTill < " . time();
        $this->reg->database->qry($qry);
        
    }
}

?>