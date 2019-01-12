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
     * ensureArray
     * 
     * @param mixed $list
     * @return array
     */
    protected function ensureArray($list){
        if (is_string($list)){
            // make sure we have consistent spacing after commas
            $list = str_replace(', ', ',', $list);
            $list = explode(',', $list);
        }
        return $list;
    }

    /**
     * buildWhere
     *
     * @param mixed** $fields: A list of fields to create the WHERE clause
     * @param mixed** $ops: A list of operators to be used for comparison
     * @return string
     *
     * NOTE: mixed** indicates that it can be an array or a string that is a
     *      comma separated list.
     */
    public function buildWhere($fields = '', $ops=''){
        // if there are no filter fields, then return an empty string
        if (!isset($fields)) {
            return '';
        }
        if ($ops == '') {
            $ops = '=';
        }
        // ensure the fields are an array
        if (is_string($fields)){
            $fields = explode(',', $fields);
        }
        // ensure the ops are an array
        if (is_string($ops)){
            $ops = explode(',', $ops);
        }
        $whereHash = [];
        // created the comaparisons to a binding
        for ($f = 0; $f < count($fields); $f++){
            $op = $f < count($ops) ? $ops[$f] : $ops[0];
            $comparison = trim($op) . " :w_" . trim($fields[$f]);
            $whereHash[trim($fields[$f])] = $comparison;
        }
        // create the WHERE clause
        $where = ' WHERE 1 = 1';
        foreach ($whereHash as $field => $val){
            $where .= " AND $field $val";
        }
        return $where;
    }
    
    /**
     * buildSql
     * This method builds SQL dynamically for consistency and convenience
     * 
     * @param sqlParams $params: an object containing all parameters
     * @return string: a properly formatted SQL statement
     */
    public function buildSql(sqlParams $params){
        $table = $params->table == '' ? $this->tableName : $params->table;
        $fields = $params->fields == '' ? $this->fields : $params->fields;
        $sort = $params->sort == "" ? "" : " ORDER BY {$params->sort}";
        $limit = $params->limit == "" ? "" : " LIMIT {$params->limit}";
        $filters = '';
        if ($params->filterFields != ''){
            // if there are filters, then create the WHERE statement
            $filters = $this->buildWhere($params->filterFields, $params->filterOps);
        }
        
        if (strtolower($params->type) == 'delete'){
            return "DELETE FROM $table$filters$limit";
        }
        if (strtolower($params->type) == 'insert') {
            // remove any spaces in the field list
            $bindHolders = str_replace( " ", "", $fields);
            // replace every comma (,) with a comma, a space and a colon (, :)
            $bindHolders = str_replace(',', ', :', $bindHolders);
            return "INSERT INTO $table ($fields) "
                    . "VALUES (:$bindHolders)";
        }
        if (strtolower($params->type) == 'update'){
            // convert the strings to arrays
            $fields = explode(", ", $fields);
            // build SET statement
            $fieldValues = '';
            foreach ($fields as $f){
                $fieldValues .= (strlen($fieldValues) > 0 ? ", " : "" ) .
                    "$f = :$f";
            }
            return "UPDATE $table SET $fieldValues$filters$limit";
        }
        if ($fields == ''){
            $fields = '*';
        }
        return trim("SELECT $fields FROM $table$filters$sort$limit");
    }

    /**
     * fetchRecords
     * 
     * @param string $table
     * @param string $fields
     * @param mixed $filterFields
     * @param mixed $filterOps
     * @param mixed $filterValues
     * @param string $sort
     * @param string $limit
     * @return array of recordsets
     */
    public function fetchRecords($table = '', $fields = '', $filterFields = '', 
            $filterOps = '=', $filterValues = '=', $sort = '', $limit = ''){
        $params = new sqlParams('select', $table, $fields, $filterFields, 
                $filterOps, $sort, $limit);
        $sql = $this->buildSql($params);
        $this->db->query($sql);
        // prepare the filter parameters for binding
        $fFields = $this->ensureArray($filterFields);
        $fValues = $this->ensureArray($filterValues);
        // bind the parameters for filter
        for ($f = 0; $f < count($fFields); $f++){
            if ($f < count($fValues)){
                $this->db->bind("w_" . $fFields[$f], $fValues[$f]);
            }
        }
        return $this->db->resultset();
    }

    /**
     * addRecords
     * 
     * @param string $table
     * @param string $fields
     * @param mixed $values
     * @return boolean
     */
    public function addRecords($table = '', $fields = '', $values = ''){
        $params = new sqlParams('insert', $table, $fields);
        $sql = $this->buildSql($params);
        $this->db->query($sql);
        // prepare the parameters for binding
        $fields = $this->ensureArray($fields);
        $values = $this->ensureArray($values);
        // bind the parameters
        for ($f = 0; $f < count($fields); $f++){
            if ($f < count($values)){
                $this->db->bind($fields[$f], $values[$f]);
            }
        }
        
        if ($this->db->execute()){
            return $this->db->lastInsertId();
        }else{
            return false;
        }
    }
    
    /**
     * editRecords
     * 
     * @param string $table
     * @param string $fields
     * @param mixed $filterFields
     * @param mixed $filterOps
     * @param mixed $filterValues
     * @param string $limit
     * @param string $values
     * @return boolean
     */
    public function editRecords($table = '', $fields = '', $filterFields = '', 
            $filterOps = '=', $filterValues = '', $limit = '', $values = ''){
        $params = new sqlParams('update', $table, $fields, $filterFields, 
                $filterOps, $limit);
        $sql = $this->buildSql($params);
        $this->db->query($sql);
        // prepare the parameters for binding
        $fields = $this->ensureArray($fields);
        $values = $this->ensureArray($values);
        // bind the parameters
        for ($f = 0; $f < count($fields); $f++){
            if ($f < count($values)){
                $this->db->bind($fields[$f], $values[$f]);
            }
        }
        // prepare the filter parameters for binding
        $fFields = $this->ensureArray($filterFields);
        $fValues = $this->ensureArray($filterValues);
        // bind the parameters for filter
        for ($f = 0; $f < count($fFields); $f++){
            if ($f < count($fValues)){
                $this->db->bind("w_" . $fFields[$f], $fValues[$f]);
            }
        }
        return ($this->db->execute());
    }
    
    /**
     * deleteRecords
     * 
     * @param string $table
     * @param mixed $filterFields
     * @param mixed $filterOps
     * @param mixed $filterValues
     * @param string $limit
     * @return boolean
     */
    public function deleteRecords($table = '', $filterFields = '', 
            $filterOps = '=', $filterValues = '', $limit = ''){
        $params = new sqlParams('select', $table, $fields, $filterFields, 
                $filterOps, $sort, $limit);
        $sql = $this->buildSql($params);
        $this->db->query($sql);
        // prepare the parameters for binding
        $fFields = $this->ensureArray($filterFields);
        $fValues = $this->ensureArray($filterValues);
        // bind the parameters for filter
        for ($f = 0; $f < count($fFields); $f++){
            if ($f < count($fValues)){
                $this->db->bind("w_" . $fFields[$f], $fValues[$f]);
            }
        }
        return ($this->db->execute());
    }
}

class sqlParams {
    public $type = '';
    public $table = '';
    public $fields = '';
    public $filterFields = '';
    public $filterOps = '';
    public $sort = '';
    public $limt = '';
    
    public function __construct($type = '', $table = '', $fields = '', 
            $filterFields = '', $filterOps = '', $sort = '', $limit = '') {
        // ensure lowercase type
        $this->type = strtolower($type);
        if (is_array($fields)){
            // if fields is an array, then convert it to a string
            $fields = implode(", ", $fields);
        }
        $this->table = $table;
        $this->fields = $fields;
        $this->filterFields = $filterFields;
        $this->filterOps = $filterOps;
        $this->sort = $sort;
        $this->limit = $limit;
    }
}