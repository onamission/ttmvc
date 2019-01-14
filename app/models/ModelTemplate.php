<?php

/**
 * Description of ModelTemplate
 *
 * @author tturnquist
 */
class ModelTemplate extends Model {
    public function __construct($tableName = '', $fields = '') {
        $this->fields = 'id, firstName, lastName, age, role, status';
        parent::__construct($tableName, $fields);
    } 
    
}
