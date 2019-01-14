<?php
/**
 * TestTemplate
 * This class is a template to build new test. It is not designed to be
 * used itself. When creating a new test class:
 *      Copy this file into a new file
 *      Name the file for the model that you want to create. It should be start
 *          with 'Test' and then a PascalCase version of the the corresponding 
 *          database table name that is being used to store the data with 
 *          a .php extention
 *      Name the Model the same as you named the file (without the php extention)
 *      In the setUp() method, add any code to set up for each test
 *      Customize by creating new tests as desired using the testOne() method
 *          as a template.
 *      For an example of how to stub out data, refer to the code in 
 *          src/tests/BaseModelTest.php 
 * 
 * @author tturnquist
 */
require_once '../../app/bootstrap.php';
use \PHPUnit\Framework\TestCase;

final class TestTemplate extends TestCase{
    
    public function setUp(){
        // setup data for each test
    }
    
    // this method is just an example. Replace it with a more meaningful test.
    public function testOne(){
        $expected = 'Blah';
        $actual = 'B' .'l' . 'a' . 'h';
        $failMessage = 'Concatenation Error';
        $this->assertEquals($expected, $actual, $failMessage);
    }
}
