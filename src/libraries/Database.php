<?php
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $error;
    private $stmt;

    /**
     * __construct
     * Sets up the object on initiation
     */
    public function __construct() {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array (
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // Create a new PDO instanace
        try {
                $this->dbh = new PDO ($dsn, $this->user, $this->pass, $options);
        }		// Catch any errors
        catch ( PDOException $e ) {
                $this->error = $e->getMessage();
        }
    }

    /**
     * query
     * Prepare statement with query
     *
     * @param string $query
     */
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    /**
     * bind
     * Bind values
     *
     * @param string $param
     * @param mixed $value
     * @param integer $type
     */
    public function bind($param, $value, $type = null) {
        if (is_null ($type)) {
            switch (true) {
                case is_int ($value) :
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool ($value) :
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null ($value) :
                    $type = PDO::PARAM_NULL;
                    break;
                default :
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue(":" . $param, $value, $type);
    }

    // Execute the prepared statement
    /**
     * execute
     * Execute the prepared statement
     *
     * @return boolean
     */
    public function execute(){
        return $this->stmt->execute();
    }

    /**
     * resultset
     * Get result set as array of objects
     *
     * @return recordset
     */
    public function resultset(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * single
     * Get single record as object
     *
     * @return record
     */
    public function single(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * rowCount
     * Get record row count
     *
     * @return type
     */
    public function rowCount(){
        return $this->stmt->rowCount();
    }

    /**
     * lastInsertId
     * Returns the last inserted ID
     *
     * @return type
     */
    public function lastInsertId(){
        return $this->dbh->lastInsertId();
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
        $fields = ensureArray($fields);
        // ensure the ops are an array
        $ops = ensureArray($ops);
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
        if (!isset($params->table) || $params->table == ''){
            return '';
        }
        $table = $params->table;
        $fields = $params->fields;
        $sort = $params->sort == ""
            ? ""
            : " ORDER BY {$params->sort}";
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
        $sql = trim("SELECT $fields FROM $table$filters$sort$limit");
        return $sql;
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
        logThis($sql, 'db', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'SQL');
        $this->query($sql);
        // prepare the filter parameters for binding
        $fFields = ensureArray($filterFields);
        $fValues = ensureArray($filterValues);
        // bind the parameters for filter
        for ($f = 0; $f < count($fFields); $f++){
            if ($f < count($fValues)){
                $this->bind("w_" . $fFields[$f], $fValues[$f]);
            }
        }
        return $this->resultset();
    }

    /**
     * fetchOne
     *
     * @param string $table
     * @param string $fields
     * @param mixed $filterFields
     * @param mixed $filterOps
     * @param mixed $filterValues
     * @return a record
     */
    public function fetchOne($table = '', $fields = '', $filterFields = '',
            $filterOps = '=', $filterValues = '='){
        $params = new sqlParams('select', $table, $fields, $filterFields,
                $filterOps, '', '1');
        $sql = $this->buildSql($params);
        logThis($sql, 'db', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'SQL');
        $this->query($sql);
        // prepare the filter parameters for binding
        $fFields = ensureArray($filterFields);
        $fValues = ensureArray($filterValues);
        // bind the parameters for filter
        for ($f = 0; $f < count($fFields); $f++){
            if ($f < count($fValues)){
                $this->bind("w_" . $fFields[$f], $fValues[$f]);
            }
        }
        return $this->single();
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
        // Remove 'id' from field list
        $fields = str_replace('id, ', '', $fields);
        $params = new sqlParams('insert', $table, $fields);
        $sql = $this->buildSql($params);
        logThis($sql, 'db', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'SQL');
        $this->query($sql);
        // prepare the parameters for binding
        $fields = ensureArray($fields);

        foreach ($fields as $key) {
            if (empty($values[$key])) {
                $values[$key] = '';
            }
            $msg = $key ." | " . $values[$key];
            logThis($msg, 'app', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'BIND');
            $this->bind($key, $values[$key]);
        }

        if ($this->execute()){
            return $this->lastInsertId();
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
                $filterOps, '', $limit);
        $sql = $this->buildSql($params);
        logThis($sql, 'db', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'SQL');
        $this->query($sql);

        // bind the parameters
        foreach ($values as $key => $val) {
            if (!empty($values[$key])) {
                $this->bind($key, $values[$key]);
            }
        }

        // prepare the filter parameters for binding
        $fFields = ensureArray($filterFields);
        $fValues = ensureArray($filterValues);
        // bind the parameters for filter
        for ($f = 0; $f < count($fFields); $f++){
            if ($f < count($fValues)){
                $this->bind("w_" . $fFields[$f], $fValues[$f]);
            }
        }
        return ($this->execute());
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
        $params = new sqlParams('delete', $table, '', $filterFields,
                $filterOps, $filterValues, '', $limit);
        $sql = $this->buildSql($params);
        logThis($sql, 'db', __FILE__ . ' line: ' . __LINE__, __FUNCTION__, 'SQL');
        $this->query($sql);
        // prepare the parameters for binding
        $fFields = ensureArray($filterFields);
        $fValues = ensureArray($filterValues);
        // bind the parameters for filter
        for ($f = 0; $f < count($fFields); $f++){
            if ($f < count($fValues)){
                $this->bind("w_" . $fFields[$f], $fValues[$f]);
            }
        }
        return ($this->execute());
    }
}

/**
 * sqlParams
 * Holds values for constructing SQL statements
 */
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
        // ensure lowercase type for comparison
        $this->type = strtolower($type);
        $this->table = $table;
        $this->fields = $fields;
        $this->filterFields = $filterFields;
        $this->filterOps = $filterOps;
        $this->sort = $sort;
        $this->limit = $limit;
    }
}