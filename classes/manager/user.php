<?php

/**
 * user.php
 * @package Skynet_managers
*/
/**
 * class Manager_User
 * Base class for all managers
 * @version 0.1
 * @author Lucas Weijers <lucas.weijers@student.hu.nl>
 * @copyright Copyright (c) 2011, Thom Werring & Lucas Weijers
 * @package Skynet_managers
 */

class Manager_User extends Manager_Base {
        
    function __construct() {
        $this->init("user");
        
    }

    public function getUsers()
    {
        $users = $this->reg->database->prefixTable("users");
        $groupmembers = $this->reg->database->prefixTable("groupmembers");
        $groups = $this->reg->database->prefixTable("groups");
        
        $query = "SELECT * FROM ".$users;
        $result = $this->reg->database->query($query);
        
        foreach($result as $key=>$content){
            if(is_numeric($key))
            {
                //groepen van de user verkrijgen.
                $query2 = "SELECT Name FROM ".$groups." WHERE ID IN(SELECT gID FROM ".$groupmembers." WHERE uID = ".$content['ID'].") ";
                $result2 = $this->reg->database->query($query2);

                foreach($result2 as $key2 => $content2)
                {
                    if(is_numeric($key2))
                    {
                        $content['groups'][] = $content2['Name'];
                    }
                }

                $contents[] = $content;
            }
        }

        return $contents;
        
    }

    public function deleteUser($uid)
    {
        $this->reg->database->delete("users", array('ID' => $uid));
    }
    
    public function updateUser($uid, $args)
    {
        $this->reg->database->update("users", $args, array("ID" => $uid));
    }
    
    public function createUser($args)
    {
        return $this->reg->database->insert("users", $args);
    }
    
    public function checkForm($verplicht, $trigger)
    {
        if(isset($_POST[$trigger]))
        {
            $errorItem = array();
            foreach($verplicht as $key => $value)
            {
                if(empty($_POST[$key]))
                {
                    $errorItem[] = $value;
                }
            }
            
            $msg['items'] = $errorItem;
            
            if(count($errorItem) > 0)
            {
                $msg['header'] = 'The following fields where left blank or where not entered correctly: ';
                return $msg;
            }
            else
            {
                return true;
            }
        }
    }
}
?>