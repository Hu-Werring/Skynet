<?php

/*
 * class Core_Database
 */

class Core_Database {
    
    private $reg = null;
    private $sql = null;
    
    /**
     * __construct
     * Constructor, initialize class, connects to mysql
     * and adds itself to registery
    */
    
    function __construct() {
        $this->reg = Core_Registery::singleton();
        if(is_null($this->reg->sql)) {
            $db_host = $this->reg->config->getvar("db_host");
            $db_user = $this->reg->config->getvar("db_user");
            $db_pass = $this->reg->config->getvar("db_pass");
            $db_daba = $this->reg->config->getvar("db_daba");
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
        if(!isset($table,$select)){
            return false;
        }
        $qry = "SELECT " . $select . " FROM " . $table . " " . $advance . ";";
        $result = $this->sql->query($qry);
        if($result){
            while($row = $result->fetch_assoc()){
                $rows[] = $row;
            }
            $rows['affected'] = $result->num_rows;
            $result->close();
        } else {
            $rows["sqlError"]   = $this->sql->error;
            $rows["sqlErrno"] = $this->sql->errno;
        }
        return $rows;
    }
    
    
    /**
     * insert
     * Inserts row into table
     * @param String Table name
     * @param Array $data assoc array with data to be inserted
     * @param Boolean succes
    */
    public function insert($table,$data){
        if(!isset($table) || !is_array($data)){
            return false;
        }
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
        return $this->sql->query($query);
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
        $query = "DELETE FROM " . $table . " WHERE ";
        $whereqry = "";
        foreach($where as $field=>$value){
            $whereqry.= $field . "= '".$value."' " . $type . " ";
        }
        $whereqry = substr($whereqry,0,-4);
        
        $query .= $whereqry;
        $result = $this->sql->query($query);
        
        if($result){
            $rows['affected'] = $this->sql->affected_rows;
        } else {
            $rows["sqlError"]   = $this->sql->error;
            $rows["sqlErrno"] = $this->sql->errno;
        }
        return $rows;
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
        $result = $this->sql->query($query);
        if($result){
            $rows['affected'] = $this->sql->affected_rows;
        } else {
            $rows["sqlError"]   = $this->sql->error;
            $rows["sqlErrno"] = $this->sql->errno;
        }
        return $rows;
    }
    
    /**
     * query
     * executes complete SQL query and tries to return data as good as possible
     * @param String $qry a mysqlQry
     * @return Array array with return data
    */
    public function query($qry){
        $result = $this->sql->query($qry);
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
    }
    
    /**
     * clearTable
     * truncates an entire table
     * @param String $table Table name
     * @return Boolean succes
    */
    public function clearTable($table){
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
}

?>