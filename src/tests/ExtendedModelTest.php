<?php
require_once '../../app/bootstrap.php';
use PHPUnit\Framework\TestCase;

class exampleModel extends Model {
    private $fields = ['id', 'firstName', 'age', 'dob', 'isCustomer'];
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
    public function testValidationHappyPath() {
        $model = new exampleModel();
        $fieldsList = 'id, firstName, age, dob, isCustomer';
        $valuesList = [123, 'Tim', 56.6, '1962-08-29 06:12:54', true];
        $expected = true;
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Not True');
        
        $expected = true;
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Doesn\'t catch bad STRING');
        
        $expected = true;
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Doesn\'t catch bad INTEGER');
        
        $expected = true;
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Doesn\'t catch bad FLOAT');
        
        $expected = true;
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Doesn\'t catch bad BOOLEAN');
        
        $expected = true;
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Doesn\'t catch bad DATE');
    }
    
    public function testValidationVariations() {
        $model = new exampleModel();
        $fieldsList = 'id, firstName, age, dob, isCustomer';
        // Integer as string.
        $valuesList = ['123', 'Tim', 56.6, '1962-08-29 06:12:54', true];
        $expected = true;
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Variations: Integer as String');
        // Float as string.
        $valuesList = [123, 'Tim', '56.6', '1962-08-29 06:12:54', true];
        $expected = true;
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Variations: Float as String');
        // Date without time.
        $valuesList = ['123', 'Tim', '56.6', '1962-08-29 06:12:54', true];
        $expected = true;
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Variations: Date no Time');
        // Boolean as INTEGER.
        $valuesList = ['123', 'Tim', '56.6', '1962-08-29 06:12:54', 1];
        $expected = true;
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Variations: Boolean as Integer');
    }
    
    public function testValidationExceptions() {
        $model = new exampleModel();
        $fieldsList = 'id, firstName, age, dob, isCustomer';
        // NaN Integer
        $valuesList = ['blah', 'Tim', 56.6, '1962-08-29', true];
        $expected = 'Validation Error: id should be a INTEGER, but blah given';
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Integer Type Exception');
        // Not a String
        $valuesList = [123, 123, '56.6', '1962-08-29', true];
        $expected = 'Validation Error: firstName should be a STRING, but 123 given';
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Date Format Exception');
        // Not a Float
        $valuesList = [123, 'Tim', 'blah', '1962-08-29', true];
        $expected = 'Validation Error: age should be a FLOAT, but blah given';
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Date Format Exception');
        // Date in MM-DD-YYYY format
        $valuesList = ['123', 'Tim', '56.6', '08-29-1962', true];
        $expected = 'Validation Error: dob should be a DATE, but 08-29-1962 given';
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Date Format Exception');
        // Not a Boolean
        $valuesList = [123, 'Tim', 56.6, '1962-08-29', 'blah'];
        $expected = 'Validation Error: isCustomer should be a BOOLEAN, but blah given';
        $actual = $model->validate($fieldsList, $valuesList);
        $this->assertEquals($expected, $actual, 'Validation Date Format Exception');
    }
}
