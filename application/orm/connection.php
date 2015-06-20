<?php
namespace ORM;

class Connection {
    
    private static $HOST = "localhost";
    private static $SCHEMA = "schemadb";
    private static $USER = "userdb";
    private static $PASSWORD = "passworddb";
    
    private $connection;
    
    public function __construct() {
        $this->open();
    }
    
    public function __destruct() {
        $this->close();
    }
    
    public function open(){
        $this->connection = new mysqli(
            connection::$HOST, connection::$USER, connection::$PASSWORD, connection::$SCHEMA
        );
        
        if ($this->connection->connect_error){
            die("({$this->connection->connect_errno()}) {$this->connection->connect_error}");
        }
        
        $this->connection->set_charset("utf-8");
    }
    
    public function close(){
        $this->connection->close();
    }
    
    public function execute($qry){
        return $this->connection->query($qry);
    }
    
    public function fetch($result){
        return $result->fetch_array(MYSQLI_ASSOC);
    }
    
    public function escape($string){
        return $this->connection->escape_string($string);
    }
    
    public function lastId(){
        return $this->connection->insert_id;
    }
    
}