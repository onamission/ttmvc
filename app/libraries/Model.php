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
    
    /**
     * fetchAll
     * Fetches multiple Records
     * 
     * @param mixed $filterFields: What fields do we filter on?
     * @param mixed $filterOps: What operations do we filter?
     * @param mixed $filterValues: What values are we looking for?
     * @param string $sort: How do we want it sorted?
     * @param string $limit: Is there a limit on records to retrieve?
     * @return array of records
     */
    public function fetchAll($filterFields = '', $filterOps = '',
            $filterValues = '', $sort = '', $limit = ''){
      return $this->db->fetchRecords($this->tableName, $this->fields, 
              $filterFields, $filterOps, $filterValues, $sort, $limit);
    }

    /**
     * fetchById
     * Fetch ONE Record By ID
     * 
     * @param integer $id: The ID of the record to retrieve
     * @return record
     */
    public function fetchById($id){
      return $this->db->fetchOne($this->tableName, $this->fields, 'id', '=',
              $id, '', '1');
    }

    /**
     * fetchOneByAttr
     * Fetch ONE Record By Property or Attribute
     * 
     * @param string $attr: The name of the attribute
     * @param string $value: The value we are searching for
     * @return record
     */
    public function fetchOneByAttr($attr, $value){
      return $this->db->fetchOne($this->tableName, $this->fields, $attr, '=',
              $value, '', '1');
    }

    /**
     * add
     * Add a single Record
     * 
     * @param string $values: a list of values
     * @param string $fields: a list of fields
     * @return boolean
     */
    public function add($values, $fields = ''){
        // allow for the option of only sending in VALUES and assuming that
        // all fields will have values added
        if ($fields == ''){
            $fields = $this->fields;
        }
        return $this->db->addRecords($this->tableName, $fields, $values);
    }

    /**
     * update
     * Update Record(s)
     * 
     * @param string $values: A list of new values
     * @param string $fields: A list of fields to change
     * @param mixed $filterFields: A list of field(s) to filter on
     * @param mixed $filterOps: A list of operators for the filter(s)
     * @param mixed $filterValues: A list of value(s) to find
     * @return boolean
     */
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

    /**
     * delete
     * Delete a Record by ID
     * 
     * @param integer $id: The ID of the record to delete
     * @return boolean
     */
    public function delete($id){
        return $this->db->deleteRecords($this->tableName, '', 'id', '=', $id, '1');
    }
    
    /**
     * save
     * Save the current record (object). If this is a new record, then
     * we need to add it. If it already exists (meaning it has an ID)
     * then we need to update the record
     * 
     * @return boolean
     */
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
