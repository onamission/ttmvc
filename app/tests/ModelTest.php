<?php
require_once '../bootstrap.php';
use \PHPUnit\Framework\TestCase;

class TestModel extends Model{}

final class ModelTest extends TestCase{
    public function testModelTableDefault():void{
        $m = new TestModel();
        
        // verify that it works without any parameters
        $expected = 'SELECT * FROM testmodel';
        $params = new sqlParams();
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
    }
    
    public function testWhere():void{
        $m = new TestModel();
        
        // verify simplest WHERE (one field and default operator '=')
        $expected = ' WHERE 1 = 1 AND id = :w_id';
        $actual = $m->buildWhere('id');
        $this->assertEquals($expected, $actual);
        
        // verify multiple fields from a string
        $expected = ' WHERE 1 = 1 AND id = :w_id AND name = :w_name';
        $actual = $m->buildWhere('id, name');
        $this->assertEquals($expected, $actual);
        
        // verify multiple fields from an array
        $expected = ' WHERE 1 = 1 AND id = :w_id AND name = :w_name';
        $actual = $m->buildWhere(['id', 'name']);
        $this->assertEquals($expected, $actual);
        
        // verify non-equals operator from a string
        $expected = ' WHERE 1 = 1 AND id > :w_id AND name = :w_name';
        $actual = $m->buildWhere('id, name', ' >  ,  = ');
        $this->assertEquals($expected, $actual);
        
        // verify non-equals operator from an array
        $expected = ' WHERE 1 = 1 AND id > :w_id AND name = :w_name';
        $actual = $m->buildWhere('id, name', [' > ' , ' = ']);
        $this->assertEquals($expected, $actual);
    }
    
    public function testBuildSql_Select():void{
        $m = new TestModel();
        
        // verify that it works without any parameters
        $expected = 'SELECT * FROM testmodel';
        $params = new sqlParams();
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works using 'SELECT' type
        $expected = 'SELECT * FROM testmodel';
        $params = new sqlParams('Select');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works when overwriting table
        $expected = 'SELECT * FROM chickens';
        $params = new sqlParams('select', 'chickens');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works with default fields
        $m->setAttribute("fields", "id, name");
        $expected = 'SELECT id, name FROM testmodel';
        $params = new sqlParams('');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works when overwriting fields
        $expected = 'SELECT description, age FROM testmodel';
        $params = new sqlParams('', '', 'description, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterFields from a string
        $expected = 'SELECT description, age FROM testmodel ' .
                'WHERE 1 = 1 AND id = :w_id AND age = :w_age';
        $params = new sqlParams('', '', 'description, age', 'id, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterFields from an array
        $expected = 'SELECT description, age FROM testmodel ' .
                'WHERE 1 = 1 AND id = :w_id AND age = :w_age';
        $params = new sqlParams('', '', 'description, age', ['id', 'age']);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterOps from a string
        $expected = 'SELECT id, name FROM testmodel ' .
                'WHERE 1 = 1 AND id = :w_id AND age > :w_age';
        $params = new sqlParams('', '', '', 'id, age', ' =, > ');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterOps from an array
        $expected = 'SELECT id, name FROM testmodel ' .
                'WHERE 1 = 1 AND id = :w_id AND age > :w_age';
        $params = new sqlParams('', '', '', 'id, age', [ '=', '>' ]);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterOps from an array with too many 
        // elements
        $expected = 'SELECT id, name FROM testmodel ' .
                'WHERE 1 = 1 AND id = :w_id AND age > :w_age';
        $params = new sqlParams('', '', '', 'id, age', [ '=', '>', '<' ]);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterOps from an array with not enough 
        // elements
        $expected = 'SELECT id, name FROM testmodel ' .
                'WHERE 1 = 1 AND id > :w_id AND age > :w_age';
        $params = new sqlParams('', '', '', 'id, age', [ '>' ]);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing a SORT parameter
        $expected = 'SELECT id, name FROM testmodel ORDER BY id';
        $params = new sqlParams('', '', '', '', '', 'id');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing a WHERE and SORT parameters
        $expected = 'SELECT id, name FROM testmodel ' .
                'WHERE 1 = 1 AND id > :w_id AND age > :w_age ORDER BY id';
        $params = new sqlParams('', '', '', 'id, age', [ '>' ], 'id');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works with a LIMIT clause
        $expected = 'SELECT id, name FROM testmodel LIMIT 4';
        $params = new sqlParams('', '', '', '', '', '', '4');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works with a LIMIT and OFFSET clause
        $expected = 'SELECT id, name FROM testmodel LIMIT 1, 4';
        $params = new sqlParams('', '', '', '', '', '', '1, 4');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
    }  
    
    public function testBuildSql_Insert():void{
        $m = new TestModel();
        
        // verify that it works with default table and fields
        $m->setAttribute("fields", "id, name");
        $expected = 'INSERT INTO testmodel (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works when overwriting table
        $expected = 'INSERT INTO chickens (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert', 'chickens');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works when overwriting fields
        $expected = 'INSERT INTO testmodel (description, age) VALUES (:description, :age)';
        $params = new sqlParams('insert', '', 'description, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores the filterFields parameter
        $expected = 'INSERT INTO testmodel (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert', '', '', 'id, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores the filterOps parameter
        $expected = 'INSERT INTO testmodel (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert', '', '', '', ' =, > ');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores the SORT parameter
        $expected = 'INSERT INTO testmodel (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert', '', '', '', '', 'id');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores a LIMIT parameter
        $expected = 'INSERT INTO testmodel (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert', '', '', '', '', '', '1, 4');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
    }
    
    public function testBuildSql_Update():void{
        $m = new TestModel();
        
        // verify that it works with default table and fields
        $m->setAttribute("fields", "id, name");
        $expected = 'UPDATE testmodel SET id = :id, name = :name';
        $params = new sqlParams('update');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works when overwriting table
        $expected = 'UPDATE chickens SET id = :id, name = :name';
        $params = new sqlParams('update', 'chickens');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works when overwriting fields
        $expected = 'UPDATE testmodel SET description = :description, age = :age';
        $params = new sqlParams('update', '', 'description, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a WHERE from the filterFields parameter
        $expected = 'UPDATE testmodel SET id = :id, name = :name' . 
                ' WHERE 1 = 1 AND id = :w_id AND age = :w_age';
        $params = new sqlParams('update', '', '', 'id, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a WHERE the filterOps parameter
        $expected = 'UPDATE testmodel SET id = :id, name = :name' . 
                ' WHERE 1 = 1 AND id > :w_id AND age < :w_age';
        $params = new sqlParams('update', '', '', 'id, age', ' >, < ');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores the SORT parameter
        $expected = 'UPDATE testmodel SET id = :id, name = :name';
        $params = new sqlParams('update', '', '', '', '', 'id');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a LIMIT clause
        $expected = 'UPDATE testmodel SET id = :id, name = :name LIMIT 1, 4';
        $params = new sqlParams('update', '', '', '', '', '', '1, 4');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
    }
    
    public function testBuildSql_Delete():void{
        $m = new TestModel();
        
        // verify that it works with default table
        $m->setAttribute("fields", "id, name");
        $expected = 'DELETE FROM testmodel';
        $params = new sqlParams('delete');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works when overwriting table
        $expected = 'DELETE FROM chickens';
        $params = new sqlParams('delete', 'chickens');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores fields parameter
        $expected = 'DELETE FROM testmodel';
        $params = new sqlParams('delete', '', 'description, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a WHERE from the filterFields parameter
        $expected = 'DELETE FROM testmodel WHERE 1 = 1 AND id = :w_id AND age = :w_age';
        $params = new sqlParams('delete', '', '', 'id, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a WHERE the filterOps parameter
        $expected = 'DELETE FROM testmodel WHERE 1 = 1 AND id > :w_id AND age < :w_age';
        $params = new sqlParams('delete', '', '', 'id, age', ' >, < ');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores the SORT parameter
        $expected = 'DELETE FROM testmodel';
        $params = new sqlParams('delete', '', '', '', '', 'id');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a LIMIT clause
        $expected = 'DELETE FROM testmodel LIMIT 4';
        $params = new sqlParams('delete', '', '', '', '', '', '4');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
    }
}