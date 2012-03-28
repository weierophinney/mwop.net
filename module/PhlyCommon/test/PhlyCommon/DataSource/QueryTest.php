<?php
namespace PhlyCommon\DataSource;

use PHPUnit_Framework_TestCase as TestCase;

class QueryTest extends TestCase
{
    public function setUp()
    {
        $this->query = new Query();
    }

    public function testAggregatesWhereClausesAsAQueue()
    {
        $this->query->where('foo', '=', 'bar')
                    ->orWhere('bar', 'IS NOT NULL')
                    ->where('baz', '!=', 'bat');
        $clauses = $this->query->getWhereClauses();
        $expected = array(
            new Where('and', 'foo', '=', 'bar'),
            new Where('or', 'bar', 'IS NOT NULL', null),
            new Where('and', 'baz', '!=', 'bat'),
        );
        foreach ($expected as $clause) {
            $test = array_shift($clauses);
            $this->assertEquals((array) $clause, (array) $test);
        }
    }

    public function testAggregatesLimitAndOffset()
    {
        $this->query->limit(10, 15);
        $this->assertEquals(10, $this->query->getLimit());
        $this->assertEquals(15, $this->query->getOffset());
    }

    public function testRepeatedCallsToLimitOverwriteLimitAndOffset()
    {
        $this->query->limit(10, 15)
                    ->limit(20, 30);
        $this->assertEquals(20, $this->query->getLimit());
        $this->assertEquals(30, $this->query->getOffset());
    }
}
