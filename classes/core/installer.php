<?php

/*
 * class Core_Installer
 */

class Core_Installer {
    
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg = null;
    
    
    /*
     * __construct()
     */
    
    function __construct() {
        $this->reg = Core_Registery::singleton();
        $this->createTables();
    }
    
    public function createTables(){
        /*$dbStruct = base64_decode(file_get_contents(basedir . "install" . DS . "databaseStructure"));
    
        $tables = json_decode($dbStruct,true);
    
        $output = "";*/
$tables['AllowedMIMETypes'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT" ,
  "MIME"=> "VARCHAR(255) NOT NULL" ,
  "Access"=> "INT(10) NOT NULL" ,
  "PRIMARY KEY"=> "(`ID`, `MIME`)" ,
  "UNIQUE INDEX"=> "`MIME` (`MIME` ASC)" ,
  "CONSTRAINT `fk_skynet_AllowedMIMETypes_skynet_Media1`"=> "
    FOREIGN KEY (`MIME` )
    REFERENCES `project4`.`skynet_Media` (`MIME` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION"
);


$tables['PageContent'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT" ,
  "pID"=> "INT(10) NULL DEFAULT NULL" ,
  "aID"=> "INT(10) NULL DEFAULT NULL" ,
  "type"=> "INT(1) NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`)"
);


$tables['Artikelen'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT" ,
  "Titel"=> "VARCHAR(25) NULL DEFAULT NULL" ,
  "cID"=> "INT(10) NULL DEFAULT NULL" ,
  "Content"=> "TEXT NULL DEFAULT NULL" ,
  "LastUpdate"=> "INT(10) NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`)" ,
  "CONSTRAINT `fk_skynet_Artikelen_skynet_PageContent1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_PageContent` (`aID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION"
);


$tables['Categorieen'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT ",
  "Naam"=> "VARCHAR(25) NULL DEFAULT NULL ",
  "Description"=> "VARCHAR(255) NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`)" ,
  "CONSTRAINT `fk_skynet_Categorieen_skynet_PageContent1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_PageContent` (`aID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION",
  "CONSTRAINT `fk_skynet_Categorieen_skynet_Artikelen1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_Artikelen` (`cID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION"
);


$tables['Forms'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT" ,
  "Naam"=> "VARCHAR(50) NULL DEFAULT NULL" ,
  "Fields"=> "TEXT NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`)" ,
  "CONSTRAINT `fk_skynet_Forms_skynet_PageContent1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_PageContent` (`aID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION"
);


$tables['GroupAccess'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT" ,
  "AccessFor"=> "VARCHAR(45) NULL DEFAULT NULL" ,
  "Access"=> "INT(5) NULL DEFAULT NULL" ,
  "gID"=> "INT(10) NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`)"
);


$tables['GroupMembers'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT" ,
  "uID"=> "INT(10) NULL DEFAULT NULL" ,
  "gID"=> "INT(10) NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`)"
);


$tables['Groups'] = array(
  "ID"=> "INT(10) NOT NULL" ,
  "Name"=> "VARCHAR(45) NULL DEFAULT NULL" ,
  "Description"=> "VARCHAR(255) NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`)" ,
  "UNIQUE INDEX"=> "`Name` (`Name` ASC)" ,
  "CONSTRAINT `fk_skynet_Groups_skynet_GroupMembers1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_GroupMembers` (`gID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION",
  "CONSTRAINT `fk_skynet_Groups_skynet_GroupAccess1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_GroupAccess` (`gID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION"
);

$tables['Links'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT" ,
  "Naam"=> "VARCHAR(50) NULL DEFAULT NULL" ,
  "Titel"=> "VARCHAR(100) NULL DEFAULT NULL" ,
  "URL"=> "VARCHAR(1024) NULL DEFAULT NULL" ,
  "cID"=> "INT(10) NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`)",
  "CONSTRAINT `fk_skynet_Links_skynet_PageContent1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_PageContent` (`aID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION"
);

$tables['Media'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT" ,
  "Locatie"=> "VARCHAR(255) NOT NULL" ,
  "Name"=> "VARCHAR(25) NULL DEFAULT NULL" ,
  "Omschrijving"=> "VARCHAR(255) NULL DEFAULT NULL" ,
  "MIME"=> "VARCHAR(255) NOT NULL" ,
  "LastUpdate"=> "INT(10) NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`)"
);



$tables['Templates'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT" ,
  "Naam"=> "VARCHAR(25) NULL DEFAULT NULL" ,
  "Description"=> "VARCHAR(255) NULL DEFAULT NULL" ,
  "Locatie"=> "VARCHAR(255) NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`)"
);

$tables['Pages'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT" ,
  "Naam"=> "VARCHAR(25) NULL DEFAULT NULL" ,
  "Positie"=> "INT(10) NULL DEFAULT NULL" ,
  "Zichtbaar"=> "TINYINT(1) NULL DEFAULT NULL" ,
  "tID"=> "INT(10) NULL DEFAULT NULL" ,
  "PRIMARY KEY"=> "(`ID`, `tID`)" ,
  "INDEX `fk_skynet_Pages_skynet_Templates1`"=> "(`tID` ASC)" ,
  "CONSTRAINT `fk_skynet_Pages_skynet_PageContent1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_PageContent` (`pID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION",
  "CONSTRAINT `fk_skynet_Pages_skynet_Templates1`"=> "
    FOREIGN KEY (`tID` )
    REFERENCES `project4`.`skynet_Templates` (`ID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION"
);

$tables['Reacties'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT ",
  "uID"=> "INT(10) NULL DEFAULT NULL ",
  "Time"=> "INT(10) NULL DEFAULT NULL ",
  "Comment"=> "TEXT NULL DEFAULT NULL ",
  "PRIMARY KEY"=> "(`ID`)"
);


$tables['Sessions'] = array(
  "uID"=> "INT(10) NOT NULL ",
  "sHash"=> "VARCHAR(32) NOT NULL ",
  "ValidTill"=> "INT(10) UNSIGNED NULL DEFAULT '0' ",
  "PRIMARY KEY"=> "(`uID`)"
);

$tables['Statistieken'] = array(
  "ID"=> "INT(10) NOT NULL AUTO_INCREMENT ",
  "IP"=> "VARCHAR(15) NULL DEFAULT NULL ",
  "Browser"=> "TEXT NULL DEFAULT NULL ",
  "Page"=> "VARCHAR(255) NULL DEFAULT NULL ",
  "Time"=> "INT(10) NULL DEFAULT NULL ",
  "uID"=> "INT(10) NULL DEFAULT NULL ",
  "PRIMARY KEY"=> "(`ID`)"
);

$tables['Users'] = array(
  "ID"=> "INT(10) NULL AUTO_INCREMENT" ,
  "Name"=> "VARCHAR(50) NULL DEFAULT NULL" ,
  "Pass"=> "VARCHAR(40) NULL DEFAULT NULL" ,
  "Email"=> "VARCHAR(50) NULL DEFAULT NULL" ,
  "UNIQUE INDEX"=> "`Name` (`Name` ASC, `Email` ASC)" ,
  "PRIMARY KEY"=> "(`ID`)" ,
  "CONSTRAINT `fk_skynet_Users_skynet_Sessions`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_Sessions` (`uID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION",
  "CONSTRAINT `fk_skynet_Users_skynet_Reacties1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_Reacties` (`uID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION",
  "CONSTRAINT `fk_skynet_Users_skynet_GroupMembers1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_GroupMembers` (`ID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION",
  "CONSTRAINT `fk_skynet_Users_skynet_Statistieken1`"=> "
    FOREIGN KEY (`ID` )
    REFERENCES `project4`.`skynet_Statistieken` (`uID` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION"
);

var_export($tables);
        
        $output="";
        foreach($tables as $table=>$fields){
            
            $output .= $this->reg->database->prefixTable($table) . ": ";
            $succes = $this->reg->database->createTable($table,$fields,true);
            $output .= var_export($succes,true) . PHP_EOL;
            if(!$succes){
                $output .= $this->reg->database->lastError();
            }
        }
        echo nl2br($output);
    }
}

?>