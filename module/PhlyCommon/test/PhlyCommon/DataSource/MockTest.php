<?php
namespace PhlyCommon\DataSource;

use PHPUnit_Framework_TestCase as TestCase;

class MockTest extends TestCase
{
    public function setUp()
    {
        $this->mock = new Mock();
    }

    public function testCanMockQueries()
    {
        $query = new Query();
        $query->where('foo', 'eq', 'bar')
              ->orWhere('bar', 'ne', 'baz')
              ->where('baz', '>', 1)
              ->limit(10, 10);
        $return = array(
            array('id' => 1, 'foo' => 'bar', 'bar' => 'nada', 'baz' => 2),
            array('id' => 1, 'foo' => 'bar', 'bar' => 'nothing', 'baz' => 3),
        );
        $this->mock->when($query, $return);

        $query2 = new Query();
        $query2->where('foo', 'eq', 'bar')
               ->orWhere('bar', 'ne', 'baz')
               ->where('baz', '>', 1)
               ->limit(10, 10);
        $this->assertEquals($return, $this->mock->query($query2));
    }

    public function testQueryReturnsEmptyArrayWhenQueryObjectIsUnmatched()
    {
        $query = new Query();
        $query->where('foo', 'eq', 'bar')
              ->orWhere('bar', 'ne', 'baz')
              ->where('baz', '>', 1)
              ->limit(10, 10);
        $return = array(
            array('id' => 1, 'foo' => 'bar', 'bar' => 'nada', 'baz' => 2),
            array('id' => 1, 'foo' => 'bar', 'bar' => 'nothing', 'baz' => 3),
        );
        $this->mock->when($query, $return);

        $query2 = new Query();
        $query2->where('foo', 'eq', 'bar');
        $this->assertEquals(array(), $this->mock->query($query2));
    }

    public function testCreateUsesIdFromDefinitionWhenAvailable()
    {
        $definition = array(
            'id'  => 'foo',
            'bar' => 'baz',
        );

        $result = $this->mock->create($definition);
        $this->assertSame($definition, $result);
    }

    public function testCreateInsertsIdWhenNoneProvidedInDefinition()
    {
        $definition = array(
            'bar' => 'baz',
        );

        $result = $this->mock->create($definition);
        $this->assertNotSame($definition, $result);
        $this->assertArrayHasKey('id', $result);
    }

    public function testCreateRaisesExceptionIfItemWithIdExists()
    {
        $definition = array(
            'id'  => 'foo',
            'bar' => 'baz',
        );

        $result = $this->mock->create($definition);
        $this->setExpectedException('DomainException', 'already exists');
        $result = $this->mock->create($definition);
    }

    public function testUpdateRaisesExceptionIfItemWithIdDoesNotExist()
    {
        $definition = array(
            'id'  => 'foo',
            'bar' => 'baz',
        );

        $this->setExpectedException('DomainException', 'does not yet exist');
        $result = $this->mock->update($definition['id'], $definition);
    }

    public function testUpdateMergesFieldsWithExistingDefinition()
    {
        $definition = array(
            'id'  => 'foo',
            'bar' => 'baz',
        );
        $this->mock->create($definition);
        $fields = array(
            'bar' => 'BAZBAT',
            'baz' => 'bat',
        );
        $result = $this->mock->update($definition['id'], $fields);
        $this->assertEquals(array_merge($definition, $fields), $result);
    }

    public function testGetReturnsNullIfItemWithIdDoesNotExist()
    {
        $definition = array(
            'id'  => 'foo',
            'bar' => 'baz',
        );
        $this->mock->create($definition);
        $this->assertNull($this->mock->get('bar'));
    }

    public function testGetReturnsPreviouslyStoredItemIfIdExists()
    {
        $definition = array(
            'id'  => 'foo',
            'bar' => 'baz',
        );
        $this->mock->create($definition);
        $test = $this->mock->get('foo');
        $this->assertSame($definition, $test);
    }

    public function testDeleteRemovesPreviouslyStoredItems()
    {
        $definition = array(
            'id'  => 'foo',
            'bar' => 'baz',
        );
        $this->mock->create($definition);
        $this->mock->delete('foo');
        $this->assertNull($this->mock->get('foo'));
    }
}
