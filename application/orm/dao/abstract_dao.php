<?php
namespace ORM\DAO;
include_once ($APPLICATION_PATH . "orm/connection.php");
include_once ($APPLICATION_PATH . "orm/entity/abstract_entity.php");

abstract class Abstract {
    
    protected $db_table;
    protected $connection;
    protected $fields;
    
    const LIMIT = 1000;
    
    function __construct(AbstractEntity $table = NULL){
        $this->db_table = $table;
        $this->connection = new Connection();
        if ($table != NULL){
            $this->fields = $this->extractColumnNames($table);
        }
    }
    
    //Mapping
    public function map($row){
        if (is_array($row)){
            $clone = clone $this->db_table;
            foreach ($row as $key=>$value){
                //filtering fields with dbName_ prefix
                $key = str_replace($this->db_table->getDbTableName() . "_", "", $key);
                //setter function name
                $function = "set" . ucfirst($key);
                //setting value
                if (method_exists($clone, $function)){
                    $clone->$function($value);
                }
            }
            
            return $clone;
        }
        
        return NULL;
    }

    //CRUD
    public function getAll($order=NULL, $offset=NULL, $limit=NULL){
        $order_clause = ($order != NULL ? " ORDER BY " . $this->connection->escape($order) : "");
        $offset_clause = ($offset != NULL && is_numeric($offset) ? $offset . "," : "");
        $limit_clause = ($limit != NULL && is_numeric($limit) ? $limit : Dao::LIMIT);

        $entries = array();
        if ($this->db_table != NULL){
            $qry = "SELECT " . implode(",", $this->fields) .
                   " FROM " . $this->db_table->getDbTableName() . $order_clause .
                   " LIMIT {$offset_clause}{$limit_clause}";
            $result = ($this->connection->execute($qry));
            
            while (($entry = $this->connection->fetch($result)) != NULL){
                array_push($entries, $this->map($entry));
            }
        }
        
        return $entries;
    }
    
    public function find($id){
        if (Util::is_id($id) && $this->db_table != NULL){
            $qry = "SELECT " . implode(",", $this->fields) . " FROM " . $this->db_table->getDbTableName() . " WHERE id={$id}";
            $result = $this->connection->execute($qry);
            return $this->map($this->connection->fetch($result));
        }
        
        return FALSE;
    }
    
    public function save(Entity $table){
        $columns = $this->_extractColumns($table);
        
        if (count($columns) && Util::is_id($table->getId())){
            $qry = "UPDATE " . $table->getDbTableName() . " SET ";
            foreach ($columns as $key=>$value){
                $qry .= $key . "='" . $value . "', ";
            }
            $qry = substr($qry, 0, -2) . " WHERE id=" . $table->getId();

            $result = $this->connection->execute($qry);
            if ($result){
                $table->setId($this->connection->lastId());
                $this->db_table = $table;
                return $this->db_table;
            }
        }
        
        return NULL;
    }
    
    public function create(Entity $table){
        $columns = $this->_extractColumns($table);
        
        if (count($columns) && $table->getId() == NULL){
            $qry = "INSERT INTO " . $table->getDbTableName() . " (" .
                    implode(",", array_keys($columns)) .") VALUES ('" .
                    implode("','", $columns) ."')";

            $result = $this->connection->execute($qry);
            if ($result){
                $table->setId($this->connection->lastId());
                $this->db_table = $table;
                return $this->db_table;
            }
        }
        
        return NULL;
    }
    
    public function delete($id){
        if (Util::is_id($id) && $this->db_table != NULL){
            $qry = "DELETE FROM " . $this->db_table->getDbTableName() . " WHERE id={$id}";
            return $this->connection->execute($qry);
        }
        
        return FALSE;
    }
    
    //Default setters
    public function setDb_table($db_table) {
        $this->db_table = $db_table;
        if ($db_table != NULL)
            $this->fields = $this->extractColumnNames ($db_table);
    }
    
    //extra methods for controlling object
    public function extractColumnNames ($table){
        $functions = get_class_methods($table);
        $columns = array();
        foreach ($functions as $function){
            if (
                    strpos($function, "get") !== FALSE &&
                    strpos($function, "getEntity") === FALSE &&
                    $function != "getDbTableName"
            ){
                $key = strtolower (str_replace ("get", "", $function));
                array_push($columns, $key);
            }
        }
        
        return $columns;
    }
    
    private function _extractColumns ($table){
        $functions = get_class_methods($table);
        $columns = array();
        foreach ($functions as $function){
            if (
                    strpos($function, "get") !== FALSE &&
                    strpos($function, "getEntity") === FALSE &&
                    $function != "getDbTableName"
            ){
                $key = strtolower (str_replace ("get", "", $function));
                $value = $this->connection->escape($table->$function());
                if ($value != NULL){
                    $columns[$key] = $value;
                }
            }
        }
        
        return $columns;
    }
    
}