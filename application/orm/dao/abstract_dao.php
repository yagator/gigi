<?php
include_once ($APPLICATION_PATH . "orm/connection.php");
include_once ($APPLICATION_PATH . "orm/entity/abstract_entity.php");

abstract class AbstractDao {
    
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
    
    
    
}