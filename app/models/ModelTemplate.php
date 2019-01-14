<?php

/**
 * ModelTemplate
 * This class is a template to build new models with. It is not designed to be
 * used itself. When creating a new model:
 *      Copy this file into a new file
 *      Name the file for the model that you want to create. It should be a 
 *          PascalCase version of the the corresponding database table name 
 *          that is being used to store the data with a .php extention
 *      Name the Model the same as you named the file (without the php extention)
 *      In the __construct() method, change the $this->tableName variable to 
 *          match the db table name
 *      In the __construct() method, change the $this->fields variable to list
 *          the fields in your new model (should match the db table fields)
 *      In the validFieldTypes() method, change the return array to be a "hash"
 *          of fields and field types like the example shows
 *      Customize by creating new methods and/or properties or overwriting the
 *          base Model methods / properties 
 * 
 * NOTE: To create a model where the data is in JOINed tables, you can either
 *  use a View rather than a Table, but that gets strange with adding data,
 *  changing data and deleting records. The better option is to name the 
 *  Model for the base table, but then add a line in the __constructor()
 *  method to name the table to include the JOIN. For example:
 *      
 *      $this->tableName = "user u INNER JOIN roles r ON u.role_id = r.id"
 * 
 *  and then don't forget to add the table aliases to the fields like: 
 * 
 *      $this->fields = 'u.id, u.firstName, u.age, u.dob, u.isCustomer, r.roleName';
 *
 * @author tturnquist
 */
class ModelTemplate extends Model {
    public function __construct() {
        parent::__construct();
        $this->tableName = 'modeltemplate';
        $this->fields = 'id, firstName, age, dob, isCustomer';
    } 
    
    protected function validFieldTypes(): array {
        return [
            'id' => 'integer',
            'firstName' => 'string',
            'age' => 'float',
            'dob' => 'date',
            'isCustomer' => 'boolean'
        ];      
    }
}
