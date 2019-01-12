<?php

class Model {
    private $db;
    private $tableName;
    private $fields;

    /**
     * __construct
     * 
     * @param string $tableName: the name of the table(s)
     * @param string $fields: a comma separated list of field names
     */
    public function __construct($tableName = '', $fields = ''){
        $this->db = new Database;
        $this->tableName = $tableName == '' ? strtolower(get_class($this)) : $tableName;
        $this->fields = $fields;
    }
    
    /**
     * getAttribute
     * 
     * @param string $attrName: the name of the attribute
     * @return mixed
     */
    public function getAttribute($attrName){
        if (property_exists(get_class($this), $attrName)) {
            return $this->$attrName;
        }
        return '';
    }
    
    /**
     * setAttribute
     * 
     * @param string $attrName: the name of the attribute
     * @param mixed $value: the value to set it to
     */
    public function setAttribute($attrName, $value){
        $this->$attrName = $value;
    }
}
