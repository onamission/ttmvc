<?php
require_once '../bootstrap.php';
use \PHPUnit\Framework\TestCase;

final class SqlTest extends TestCase{
    
    public function testWhere():void{
        $m = new Database();
        
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
        $m = new Database();
        $t = 'posts';
        $f = 'id, name';
        
        // verify that it works using 'SELECT' type
        $expected = 'SELECT id, name FROM posts';
        $params = new sqlParams('Select', $t, $f);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works with no fields
        $expected = 'SELECT * FROM posts';
        $params = new sqlParams('Select', $t);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works with no SELECT
        $expected = 'SELECT id, name FROM posts';
        $params = new sqlParams('', $t, $f);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterFields from a string
        $expected = 'SELECT description, age FROM posts ' .
                'WHERE 1 = 1 AND id = :w_id AND age = :w_age';
        $params = new sqlParams('', $t, 'description, age', 'id, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterFields from an array
        $expected = 'SELECT description, age FROM posts ' .
                'WHERE 1 = 1 AND id = :w_id AND age = :w_age';
        $params = new sqlParams('', $t, 'description, age', ['id', 'age']);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterOps from a string
        $expected = 'SELECT id, name FROM posts ' .
                'WHERE 1 = 1 AND id = :w_id AND age > :w_age';
        $params = new sqlParams('', $t, $f, 'id, age', ' =, > ');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterOps from an array
        $expected = 'SELECT id, name FROM posts ' .
                'WHERE 1 = 1 AND id = :w_id AND age > :w_age';
        $params = new sqlParams('', $t, $f, 'id, age', [ '=', '>' ]);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterOps from an array with too many 
        // elements
        $expected = 'SELECT id, name FROM posts ' .
                'WHERE 1 = 1 AND id = :w_id AND age > :w_age';
        $params = new sqlParams('', $t, $f, 'id, age', [ '=', '>', '<' ]);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing filterOps from an array with not enough 
        // elements
        $expected = 'SELECT id, name FROM posts ' .
                'WHERE 1 = 1 AND id > :w_id AND age > :w_age';
        $params = new sqlParams('', $t, $f, 'id, age', [ '>' ]);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing a SORT parameter
        $expected = 'SELECT id, name FROM posts ORDER BY id';
        $params = new sqlParams('', $t, $f, '', '', 'id');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works passing a WHERE and SORT parameters
        $expected = 'SELECT id, name FROM posts ' .
                'WHERE 1 = 1 AND id > :w_id AND age > :w_age ORDER BY id';
        $params = new sqlParams('', $t, $f, 'id, age', [ '>' ], 'id');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works with a LIMIT clause
        $expected = 'SELECT id, name FROM posts LIMIT 4';
        $params = new sqlParams('', $t, $f, '', '', '', '4');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it works with a LIMIT and OFFSET clause
        $expected = 'SELECT id, name FROM posts LIMIT 1, 4';
        $params = new sqlParams('', $t, $f, '', '', '', '1, 4');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
    }  
    
    public function testBuildSql_Insert():void{
        $m = new Database();
        $t = 'posts';
        $f = 'id, name';
        
        // verify that it works with default table and fields
        $expected = 'INSERT INTO posts (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert', $t, $f);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores the filterFields parameter
        $expected = 'INSERT INTO posts (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert', $t, $f, 'id, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores the filterOps parameter
        $expected = 'INSERT INTO posts (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert', $t, $f, '', ' =, > ');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores the SORT parameter
        $expected = 'INSERT INTO posts (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert', $t, $f, '', '', 'id');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores a LIMIT parameter
        $expected = 'INSERT INTO posts (id, name) VALUES (:id, :name)';
        $params = new sqlParams('insert', $t, $f, '', '', '', '1, 4');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
    }
    
    public function testBuildSql_Update():void{
        $m = new Database();
        $t = 'posts';
        $f = 'id, name';
        
        // verify that it works with default table and fields
        $expected = 'UPDATE posts SET id = :id, name = :name';
        $params = new sqlParams('update', $t, $f);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a WHERE from the filterFields parameter
        $expected = 'UPDATE posts SET id = :id, name = :name' . 
                ' WHERE 1 = 1 AND id = :w_id AND age = :w_age';
        $params = new sqlParams('update', $t, $f, 'id, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a WHERE the filterOps parameter
        $expected = 'UPDATE posts SET id = :id, name = :name' . 
                ' WHERE 1 = 1 AND id > :w_id AND age < :w_age';
        $params = new sqlParams('update', $t, $f, 'id, age', ' >, < ');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores the SORT parameter
        $expected = 'UPDATE posts SET id = :id, name = :name';
        $params = new sqlParams('update', $t, $f, '', '', 'id');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a LIMIT clause
        $expected = 'UPDATE posts SET id = :id, name = :name LIMIT 1, 4';
        $params = new sqlParams('update', $t, $f, '', '', '', '1, 4');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
    }
    
    public function testBuildSql_Delete():void{
        $m = new Database();
        $t = 'posts';
        
        // verify that it works with default table
        $expected = 'DELETE FROM posts';
        $params = new sqlParams('delete', $t);
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores fields parameter
        $expected = 'DELETE FROM posts';
        $params = new sqlParams('delete', $t, 'description, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a WHERE from the filterFields parameter
        $expected = 'DELETE FROM posts WHERE 1 = 1 AND id = :w_id AND age = :w_age';
        $params = new sqlParams('delete', $t, '', 'id, age');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a WHERE the filterOps parameter
        $expected = 'DELETE FROM posts WHERE 1 = 1 AND id > :w_id AND age < :w_age';
        $params = new sqlParams('delete', $t, '', 'id, age', ' >, < ');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it ignores the SORT parameter
        $expected = 'DELETE FROM posts';
        $params = new sqlParams('delete', $t, '', '', '', 'id');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
        
        // verify that it creates a LIMIT clause
        $expected = 'DELETE FROM posts LIMIT 4';
        $params = new sqlParams('delete', $t, '', '', '', '', '4');
        $actual = $m->buildSql($params);
        $this->assertEquals($expected, $actual);
    }
}