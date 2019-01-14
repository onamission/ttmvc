<?php
require_once '../../app/bootstrap.php';
use \PHPUnit\Framework\TestCase;

final class TestTemplate extends TestCase{
    
    public function setUp(){
        // setup data for each test
    }
    
    public function testOne(){
        $expected = 'Blah';
        $actual = 'B' .'l' . 'a' . 'h';
        $failMessage = 'Concatenation Error';
        $this->assertEquals($expected, $actual, $failMessage);
    }
}
