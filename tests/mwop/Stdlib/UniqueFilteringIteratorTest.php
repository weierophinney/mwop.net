<?php
namespace mwop\Stdlib;

use PHPUnit_Framework_TestCase as TestCase,
    ArrayIterator;

class UniqueFilteringIteratorTest extends TestCase
{
    public function setUp()
    {
        $this->values = array('foo', new \stdClass, array('bar'), 1, 1.1);
        $spec   = array();
        foreach ($this->values as $value) {
            $array = array_fill(0, 10, $value);
            $spec  = array_merge($spec, $array);
        }
        $this->iterator = new UniqueFilteringIterator();
        foreach ($this->values as $value) {
            $this->iterator->push($value);
        }
    }

    public function testSameEntriesAreReturnedOnlyOnceDuringIteration()
    {
        $test = array();
        foreach ($this->iterator as $value) {
            $test[] = $value;
        }
        $this->assertEquals($this->values, $test);
    }

    public function testIteratingMultipleTimesReturnsSameResults()
    {
        $test1 = array();
        $test2 = array();
        foreach ($this->iterator as $value) {
            $test1[] = $value;
        }
        foreach ($this->iterator as $value) {
            $test2[] = $value;
        }
        $this->assertEquals($this->values, $test1);
        $this->assertEquals($this->values, $test2);
        $this->assertEquals($test1, $test2);
    }
}
