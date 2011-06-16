<?php

/*
 * class Core_Database
 */

class Core_Database {
    
    
    /**
     * $reg
     * Link to registery
     * @access private
    */
    private $reg = null;
    
    /**
     * $sql
     * Link to mysqli
     * @access private
    */
    private $sql = null;
    
    /**
     * $lastQuery
     * Stores last query done
    */
    private $lastQuery = null;
    
    /**
     * __construct
     * Constructor, initialize class, connects to mysql
     * and adds itself to registery
    */
    
    function __construct() {
        $this->reg = Core_Registery::singleton();
        if(is_null($this->reg->sql)) {
            $db_host = $this->reg->settings->settings['db']['host'];
            $db_user = $this->reg->settings->settings['db']['user'];
            $db_pass = $this->reg->settings->settings['db']['pass'];
            $db_daba = $this->reg->settings->settings['db']['name'];
            $this->sql = new mysqli($db_host,$db_user,$db_pass,$db_daba);
        }
        $this->reg->database = $this;
        $this->reg->sql = $this->sql;
    }
    
    /**
     * select
     * Creates a select query and runs it.
     * @param String $table table name
     * @param String $select what fields needs to be selected
     * @param String $advance (Optional) advance mysql select query
     * @return Array all rows within the query, and an assoc entry with affected rows or array with errors
    */
    public function select($table,$select,$advance = ""){
        $table = $this->prefixTable($table);
        $qry = "SELECT " . $select . " FROM " . $table . " " . $advance . ";";
        $result = $this->query($qry);
        return $result;
    }
    
    
    /**
     * insert
     * Inserts row into table
     * @param String Table name
     * @param Array $data assoc array with data to be inserted
     * @param Boolean succes
    */
    public function insert($table,$data){
        if(!is_array($data) || !is_string($table)){
            return false;
        }
        $table = $this->prefixTable($table);
        $query = "INSERT INTO " . $table . " (";
        $fields = "";
        $values = "";
        foreach($data as $field => $value){
            $fields .= $field . ", ";
            $values .= "'".$value . "', ";
        }
        $fields = substr($fields,0,-2);
        $values = substr($values,0,-2);
        $query .= $fields . ") " . PHP_EOL .
        "VALUES (" . $values . ")";
        $result = $this->query($query);
        return $result['succes'];
    }
    
    
    /**
     * delete
     * Deletes data from table
     * @param String $table Table name
     * @param Array $where assoc array with where clause
     * @param String $type how the where clause should be separated (AND / OR)
     * @return Array an array containing affected rows or sql Error
    */
    public function delete($table,$where,$type="AND"){
        $table = $this->prefixTable($table);
        $query = "DELETE FROM " . $table . " WHERE ";
        $whereqry = "";
        foreach($where as $field=>$value){
            $whereqry.= $field . "= '".$value."' " . $type . " ";
        }
        $whereqry = substr($whereqry,0,-4);
        
        $query .= $whereqry;
        $result = $this->query($query);
        return $result;
    }
    
    /**
     * update
     * Updates data in table
     * @param String $table Table name
     * @param Array $data assoc array with data to be updated
     * @param Array $where assoc array with where clause
     * @param String $type how the where clause should be separated (AND / OR)
     * @param String $advance (Optional) advance mysql update query
     * @return Array an array containing affected rows or sql Error
    */
    public function update($table,$data,$where,$type="and",$advance=""){
        $table = $this->prefixTable($table);
        $query = "UPDATE " . $table . " SET ";
        $set = "";
        foreach($data as $field => $value){
            $set .= $field. "='" . $value . "',";
        }
        $set = substr($set,0,-1);
        $query .= $set;
        $whereqry = " WHERE ";
        foreach($where as $field=>$value){
            $whereqry.= $field . "= '".$value."' " . $type . " ";
        }
        $whereqry = substr($whereqry,0,-4);
        
        $query .= $whereqry;
        $query .= " " . $advance;
        $result = $this->query($query);
        return $result;
    }
    
    /**
     * query
     * executes complete SQL query and tries to return data as good as possible
     * REMEMBER TO PREFIX YOUR TABLE NAME! (Core_Database()->prefixTable($table))
     * @param String $qry a mysqlQry
     * @param Boolean $returnResult should the $result variable be returned in the return array (Default: false)
     * @return Array array with return data
    */
    public function query($qry,$returnResult=false){
        $this->lastQuery = $qry;
        
        $result = $this->sql->query($qry);
        if($returnResult){
            $return["result"] = $result;
        }
        switch($result) {
            case false:
                $return["error"] = $this->sql->error;
                $return["errno"] = $this->sql->errno;
                $return['succes']= false;
            break;
            case true:
                $return['affected'] = $this->sql->affected_rows;
                $return['succes'] = true;
            break;
            default:
                while($row = $result->fetch_assoc()){
                    $return[] = $row;
                }
                $return['affected'] = $result->num_rows;
                $result->close();
            break;
        }
        
        return $return;
    }
    /**
     * qry (Alias for Query)
     * executes complete SQL query and tries to return data as good as possible
     * REMEMBER TO PREFIX YOUR TABLE NAME! (Core_Database()->prefixTable($table))
     * @param String $qry a mysqlQry
     * @param Boolean $returnResult should the $result variable be returned in the return array (Default: false)
     * @return Array array with return data
    */
    public function qry($qry,$returnResult=false){
        return $this->query($qry,$returnResult);
    }
    
    /**
     * createTable
     * creates table in database
     * @param String $table Table Name
     * @param Array $fields Field data
     * Format: array(
     *      fieldname1"=>array(
     *          "type"=>"fieldtype", 
     *          "primary"=>true/false,
     *          "advance"=>"NOT NULL AUTO_INCREMENT etc"
     *      ),
     *      "fieldname2"=>array(
     *          ...
     *       )
     * )
     * @param Boolean $force Force creation of table THIS CAN PERMANENTLY DESTROY DATA
     * @param String $advance advance SQL Create query (optional)
     * @return Boolean result
    */
    public function createTable($table,$fields,$force=false,$advance="ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci"){
        $table = $this->prefixTable($table);
        if($force){
            $succes = $this->query("DROP TABLE IF EXISTS " . $table);
            if(!$succes)
                echo $this->reg->debug->print_pre($this->lastError());
        }
        $query = "CREATE TABLE IF NOT EXISTS " . $table . " (" . PHP_EOL;
        foreach($fields as $field=>$description){
            if(!isset($description["type"]) && is_array($description)){
               trigger_error("No type defined for " . $field, E_USER_ERROR);
            } elseif(!is_array($description)){
                $type = $description;
                unset($description);
            } else {
                $type = $description['type'];
            }
            $query.= $field . " " .  $type . " ";
            ;
            if(isset($description['primary']) && $description['primary'] === true){
                $query .= "PRIMARY KEY ";
            }
            if(isset($description['advance'])){
                $query .= $description['advance'];
            }
            $query .= "," . PHP_EOL;
        }
        $query = substr($query,0,-2);
        $query .= PHP_EOL . ") " . $advance;
        $result = $this->query($query,true);
        return $result['succes'];
    }
    /**
     * clearTable
     * truncates an entire table
     * @param String $table Table name
     * @return Boolean succes
    */
    public function clearTable($table){
        $table = $this->prefixTable($table);
        return $this->sql->query("Truncate table " . $table);
    }
    
    /**
     * res
     * Real Escape String escapes user submitted data before executing it by with mysql.
     * @param String $string Text needed to be escaped
     * @return String escaped string, safe to write to database
    */
    public function res($string){
        return $this->sql->real_escape_string($string);
    }
    
    /**
     * prefixTable
     * Prefixes string with the table prefix
     * @param String $table Table name without prefix
     * @return Sting Table name with prefix
    */
    public function prefixTable($table){
        return $this->reg->settings->settings['db']['prefix'] . $table;
    }
    
    public function lastError(){
        return $this->sql->errno . " " . $this->sql->error . PHP_EOL . "The complete query was \"" . $this->lastQuery . "\"" . PHP_EOL . PHP_EOL;
    }
}

