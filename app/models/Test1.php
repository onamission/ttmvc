<?php

class Test1 extends Model {
    public function __construct() {
        parent::__construct();
        $this->tableName = 'test1';
        $this->fields = 'id, name, dob, isCustomer';
    } 
    
    protected function validFieldTypes(): array {
        return [
            'id' => 'integer',
            'name' => 'string',
            'dob' => 'date',
            'isCustomer' => 'boolean'
        ];      
    }
}