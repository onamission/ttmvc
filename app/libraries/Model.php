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
    
    // Fetch All Records
    public function fetchAll($filterFields = '', $filterOps = '',
            $filterValues = '', $sort = '', $limit = ''){
      return $this->db->fetchRecords($this->tableName, $this->fields, 
              $filterFields, $filterOps, $filterValues, $sort, $limit);
    }

    // Fetch ONE Record By ID
    public function fetchById($id){
      return $this->db->fetchOne($this->tableName, $this->fields, 'id', '=',
              $id, '', '1');
    }

    // Add a Record
    public function add($values, $fields = ''){
        // allow for the option of only sending in VALUES and assuming that
        // all fields will have values added
        if ($fields == ''){
            $fields = $this->fields;
        }
        return $this->db->addRecords($this->tableName, $fields, $values);
    }

    // Update Record(s)
    public function update($values, $fields = '', $filterFields = '',
                $filterOps = '', $filterValues = ''){
        // allow for the option of only sending in VALUES and assuming that
        // all fields will have values added
        if ($fields == ''){
            $fields = $this->fields;
        }
        return $this->db->editRecords($this->tableName, $fields, $filterFields,
                $filterOps, $filterValues, $values);
    }

    // Delete a Record by ID
    public function delete($id){
        return $this->db->deleteRecords($this->tableName, '', 'id', '=', $id, '1');
    }
    
    public function save(){
        $fieldList = is_string($this->fields) 
                ? explode(',', $this->fields)
                : $this->fields;
        $valueList = [];
        foreach ($fieldList as $field) {
            array_push($valueList, property_exists($this, $field) 
                    ? $this->$field 
                    : NULL); 
        }
        $values = implode(', ', $valueList);
        // assume if there is an ID, then update, otherwise it is a new record
        if (property_exists($this, 'id')) {
            return $this->update($values, $this->fields, 'id', '=', $this->id);
        } else {
            return $this->add($values);
        }
    }
}
