<?php
namespace ORM\Entity;

abstract class Abstract {
    
    protected $id;
    protected $db_table_name;
    
    function __construct($name = ""){
        $this->db_table_name = $name;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getDbTableName(){
        return $this->db_table_name;
    }
    
}