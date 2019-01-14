<?php
require_once '../bootstrap.php';
use PHPUnit\Framework\TestCase;

class BaseModelTest extends TestCase {
        private $modelData = [
                        [
                           'id' => 1,
                           'name' => 'Tim'
                        ],
                        [
                           'id' => 2,
                           'name' => 'Bob' 
                        ]
                    ];
        private $model;
        
    public function setUp() {
        $this->model = new Model('posts', 'id, name');
    }
    
    public function testFetchAll() {
        $stub = $this->getMockBuilder(Database::class)
                    ->setMethods(['fetchRecords'])
                    ->getMock();
        $stub->method('fetchRecords')
             ->willReturn($this->modelData);
        $expected = $this->modelData;
        $this->model->setAttribute('db', $stub);
        $actual = $this->model->fetchAll();
        $this->assertEquals($expected, $actual);
    }
    
    public function testFetchById() {
        $stub = $this->getMockBuilder(Database::class)
                    ->setMethods(['fetchOne'])
                    ->getMock();
        $stub->method('fetchOne')
             ->willReturn($this->modelData[1]);
        $expected = [
                        'id' => 2,
                        'name' => 'Bob' 
                     ];
        $this->model->setAttribute('db', $stub);
        $actual = $this->model->fetchById(2);
        $this->assertEquals($expected, $actual);
    }
    
    public function testFetchOneByAttr() {
        $stub = $this->getMockBuilder(Database::class)
                    ->setMethods(['fetchOne'])
                    ->getMock();
        $stub->method('fetchOne')
             ->willReturn($this->modelData[1]);
        $expected = [
                        'id' => 2,
                        'name' => 'Bob' 
                    ];
        $this->model->setAttribute('db', $stub);
        $actual = $this->model->fetchOneByAttr('name', 'Bob');
        $this->assertEquals($expected, $actual);
    }
}