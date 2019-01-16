<?php

class Model {
    private $db;
    protected $tableName;
    protected $fields;

    /**
     * __construct
     *
     * @param string $tableName: the name of the table(s)
     * @param string $fields: a comma separated list of field names
     */
    public function __construct(){
        $this->db = new Database;
        // set the default tableName to the name of the class
        $this->tableName = strtolower(get_class($this));
    }

    /**
     * validFieldTypes
     * Returns an array of this model's field types for data validation.
     *
     * @return array
     */
    protected function validFieldTypes() {
        return [];
    }

    /**
     * validate
     * Checks to see if all data values are of the proper type for their
     * respective field
     *
     * @param array $values: An associative array of the values to validate
     * @return boolean
     */
    public function validate($values) {
        $isValid = [];
        $typeList = $this->validFieldTypes();
        print_r($values);
        foreach ($values as $key => $value) {
            if (!isset($typeList[$key])){
                continue;
            }
            $validType = $typeList[$key];
            if (
                // Integers: if not zero, but intval() = 0, then not valid
                ($validType == 'integer' && $value !== 0 && intval($value) == 0) ||
                // Floats: if not zero, but floatval() = 0, then not valid
                ($validType == 'float'  && $value !== 0 && floatval($value) == 0) ||
                // Dates: if not formatted YYYY-MM-DD[ HH:NN:SS] and strtotime() = 0, then not valid
                ($validType == 'date' &&
                        !preg_match('/^\d[4]-\d[1,2]-\d[1,2]\s*\d*:*\d*:*\d*$/', $value) &&
                        strtotime($value) == 0) ||
                // Booleans: if not FALSE AND not TRUE, then not valid
                ($validType == 'boolean' && $value != false &&
                            $value !== true && $value !== 1) ||
                // Strings: if is_string() = FALSE, then not valid
                ($validType == 'string' && is_string($value) === false)
                ){
                    array_push($isValid, "$key should be a " .
                    strtoupper($validType) . ", but $value given");
            }
        }
        if (count($isValid) == 0) {
            return true;
        }
        return "Validation Error: " . implode(", ", $isValid);
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
     * @param array $values: an associative array of values
     * @param string $fields: a list of fields
     * @return boolean
     */
    public function add($values, $fields = ''){
        // allow for the option of only sending in VALUES and assuming that
        // all fields will have values added
        if ($fields == ''){
            $fields = $this->fields;
        }
        $isValid = $this->validate($values);
        if ($isValid === true) {
            $msg = 'Validation passed with data: ' . implode(', ', $values);
            logThis($msg, 'app', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'add');
            return $this->db->addRecords($this->tableName, $fields, $values);
        }
        return $isValid;
    }

    /**
     * update
     * Update Record(s)
     *
     * @param array $values: an associative array of values
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
        if (empty($fields)){
            $fields = $this->getAttribute('fields');
        }
        $isValid = $this->validate($values);
        if ($isValid === true) {
            $msg = 'Validation passed with data: ' . implode(', ', $values);
            logThis($msg, 'app', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'update');
            return $this->db->editRecords($this->tableName, $fields, $filterFields,
            $filterOps, $filterValues, '', $values);
        }
        return $isValid;
    }

    /**
     * delete
     * Delete a Record by ID
     *
     * @param integer $id: The ID of the record to delete
     * @return boolean
     */
    public function delete($id){
        return $this->db->deleteRecords($this->tableName, 'id', '=', $id, '1');
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
