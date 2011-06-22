<?php

/**
 * page.php
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
    
    function getUsers()
    {
        $users = $this->reg->database->prefixTable("users");
        $groupmembers = $this->reg->database->prefixTable("groupmembers");
        $group = $this->reg->database->prefixTable("groups");
        
        $query = "SELECT * FROM ".$users;
        $result = $this->reg->database->query($query);
        
        foreach($result as $key=>$content){
            if(is_numeric($key))
            {
                $contents[] = $content;
            }
        }
        print_r($contents);
        return $contents;
        
    }

}
?>