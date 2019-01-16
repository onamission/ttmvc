<?php
require_once '../../app/bootstrap.php';
use PHPUnit\Framework\TestCase;

class exampleModel extends Model {
    protected $fields = ['id', 'firstName', 'age', 'dob', 'isCustomer'];
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
class extendedModelTest extends TestCase {
    public function setUp() {
        $this->values = ['id' => 123, 
            'firstName' => 'Tim', 
            'age' => 56.6, 
            'dob' => 
            '1962-08-29 06:12:54', 
            'isCustomer' => true
            ];
    }
    public function testValidationHappyPath() {
        $model = new exampleModel();
        $valuesList = $this->values;
        $expected = true;
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Not True');
        
        $expected = true;
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Doesn\'t catch bad STRING');
        
        $expected = true;
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Doesn\'t catch bad INTEGER');
        
        $expected = true;
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Doesn\'t catch bad FLOAT');
        
        $expected = true;
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Doesn\'t catch bad BOOLEAN');
        
        $expected = true;
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Doesn\'t catch bad DATE');
    }
    
    public function testValidationVariations() {
        $model = new exampleModel();
        // Integer as string.
        $valuesList = $this->values;
        $valuesList['id'] = '123';
        $expected = true;
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Variations: Integer as String');
        // Float as string.
        $valuesList = $this->values;
        $valuesList['age'] = '56.6';
        $expected = true;
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Variations: Float as String');
        // Date without time.
        $valuesList = $this->values;
        $valuesList['dob'] = '1962-08-29';
        $expected = true;
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Variations: Date no Time');
        // Boolean as INTEGER.
        $valuesList = $this->values;
        $valuesList['isCustomer'] = 1;
        $expected = true;
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Variations: Boolean as Integer');
    }
    
    public function testValidationExceptions() {
        $model = new exampleModel();
        // NaN Integer
        $valuesList = $this->values;
        $valuesList['id'] = 'blah';
        $expected = 'Validation Error: id should be a INTEGER, but blah given';
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Integer Type Exception');
        // Not a String
        $valuesList = $this->values;
        $valuesList['firstName'] = 123;
        $expected = 'Validation Error: firstName should be a STRING, but 123 given';
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Date Format Exception');
        // Not a Float
        $valuesList = $this->values;
        $valuesList['age'] = 'blah';
        $expected = 'Validation Error: age should be a FLOAT, but blah given';
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Date Format Exception');
        // Date in MM-DD-YYYY format
        $valuesList = $this->values;
        $valuesList['dob'] = '08-29-1962';
        $expected = 'Validation Error: dob should be a DATE, but 08-29-1962 given';
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Date Format Exception');
        // Not a Boolean
        $valuesList = $this->values;
        $valuesList['isCustomer'] = 'blah';
        $expected = 'Validation Error: isCustomer should be a BOOLEAN, but blah given';
        $actual = $model->validate($valuesList);
        $this->assertEquals($expected, $actual, 'Validation Date Format Exception');
    }
}
