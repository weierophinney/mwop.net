<?php
namespace mwop\Resource;

use PHPUnit_Framework_TestCase as TestCase,
    mwop\DataSource\Mongo as MongoDataSource,
    mwop\DataSource\Query,
    mwop\Entity\Entry,
    Zend\SignalSlot\Signals,
    DateTime,
    DateInterval,
    Mongo;

class EntryResourceTest extends TestCase
{
    public function setUp()
    {
        EntryResource::resetSignals();
        $this->mongo      = new Mongo();
        $this->mongoDb    = $this->mongo->mwoptest;
        $this->collection = $this->mongoDb->entries;

        $this->collection->drop();

        $this->dataSource = new MongoDataSource($this->collection);
        $this->resource   = new EntryResource();
        $this->resource->setDataSource($this->dataSource)
                       ->setCollectionClass('mwop\Resource\MongoCollection');
    }

    public function getItem()
    {
        return array(
            'id'        => 'some-slug',
            'title'     => 'Some Slug',
            'body'      => 'Some Slug.',
            'author'    => 'matthew',
            'is_draft'  => false,
            'is_public' => true,
            'created'   => strtotime('today'),
            'updated'   => strtotime('today'),
            'timezone'  => 'America/New_York',
            'tags'      => array('foo', 'bar'),
            'version'   => 2,
        );
    }

    public function getItems()
    {
        return array(
            array(
                'id'        => 'some-slug',
                'title'     => 'Some Slug',
                'body'      => 'Some Slug.',
                'author'    => 'matthew',
                'is_draft'  => false,
                'is_public' => true,
                'created'   => strtotime('today'),
                'updated'   => strtotime('today'),
                'timezone'  => 'America/New_York',
                'tags'      => array('foo', 'bar'),
                'version'   => 2,
            ),
            array(
                'id'        => 'some-other-slug',
                'title'     => 'Some Other Slug',
                'body'      => 'Some other slug.',
                'author'    => 'matthew',
                'is_draft'  => true,
                'is_public' => true,
                'created'   => strtotime('yesterday'),
                'updated'   => strtotime('today'),
                'timezone'  => 'America/New_York',
                'tags'      => array('foo'),
                'version'   => 2,
            ),
            array(
                'id'        => 'some-final-slug',
                'title'     => 'Some Final Slug',
                'body'      => 'Some final slug.',
                'author'    => 'matthew',
                'is_draft'  => false,
                'is_public' => true,
                'created'   => strtotime('2 days ago'),
                'updated'   => strtotime('yesterday'),
                'timezone'  => 'America/New_York',
                'tags'      => array('bar'),
                'version'   => 2,
            )
        );
    }

    public function testGetAllReturnsMongoCollection()
    {
        $test = $this->resource->getAll();
        $this->assertInstanceOf('mwop\Resource\MongoCollection', $test);
    }

    public function testGetReturnsNullIfItemNotFound()
    {
        $this->assertNull($this->resource->get('foo'));
    }


    public function testGetReturnsEntryWhenFound()
    {
        $item = $this->getItem();
        $this->dataSource->create($item);
        $test = $this->resource->get('some-slug');
        $this->assertInstanceOf('mwop\Entity\Entry', $test);
    }

    public function testCreateReturnsEntryWithValidArrayData()
    {
        $item  = $this->getItem();
        $entry = $this->resource->create($item);
        $this->assertInstanceOf('mwop\Entity\Entry', $entry);
        $this->assertEquals($item['id'], $entry->getId());
    }

    public function testCreateAcceptsEntryObject()
    {
        $item  = $this->getItem();
        $entry = new Entry();
        $entry->fromArray($item);
        $test = $this->resource->create($entry);
        $this->assertInstanceOf('mwop\Entity\Entry', $test);
        $this->assertSame($entry, $test);
    }

    public function testCreateReturnsInputFilterOnArraySpecWithInvalidData()
    {
        $test = $this->resource->create(array());
        $this->assertInstanceOf('Zend\Filter\InputFilter', $test);
    }

    public function testCreateReturnsInputFilterForInvalidEntry()
    {
        $entry = new Entry();
        $test = $this->resource->create($entry);
        $this->assertInstanceOf('Zend\Filter\InputFilter', $test);
    }

    public function testCreateThrowsExceptionWithInvalidSpec()
    {
        $this->setExpectedException('InvalidArgumentException', 'array or object');
        $this->resource->create('foo');
    }

    public function testUpdateRaisesExceptionOnNonexistentItem()
    {
        $this->setExpectedException('DomainException', 'does not exist');
        $this->resource->update('foo', array('title' => 'updated title'));
    }

    public function testUpdateRaisesExceptionOnInvalidSpecification()
    {
        $item = $this->getItem();
        $this->dataSource->create($item);
        $this->setExpectedException('InvalidArgumentException', 'Expected an array or object');
        $this->resource->update('some-slug', 'foobar');
    }

    public function testDeleteReturnsFalseIfEntryDoesNotExist()
    {
        $this->assertFalse($this->resource->delete('foo-bar'));
    }

    public function testDeleteRemovesEntryIfItExists()
    {
        $item = $this->getItem();
        $this->dataSource->create($item);
        $this->assertTrue($this->resource->delete($item['id']));
        $this->assertNull($this->resource->get($item['id']));
    }

    public function testDeleteCanAcceptEntryObjects()
    {
        $item  = $this->getItem();
        $entry = $this->resource->create($item);
        $this->assertTrue($this->resource->delete($entry));
        $this->assertNull($this->resource->get($item['id']));
    }

    public function runQueryComparisonTests($items, $method)
    {
        $args  = func_get_args();
        $args  = array_slice($args, 2);

        foreach ($items as $item) {
            $entity = $this->resource->create($item);
        }

        $collection = call_user_func_array(array($this->resource, $method), $args);

        $limit = array_pop($args);
        $this->assertEquals($limit, count($collection));

        // Check that ids are in correct order
        $start      = array_pop($args);
        $end        = $limit + $start;
        $start++;
        $first      = false;
        $last       = false;
        foreach ($collection as $entity) {
            $id = $entity->getId();
            $this->assertArrayHasKey($id, $items);
            if (!$first) {
                $first = $id;
            }
            $last = $id;
        }
        $this->assertEquals('some-slug-' . $end, $first);
        $this->assertEquals('some-slug-' . $start, $last);

        // Check that created dates are in appropriate order (reverse by date)
        $ts = false;
        foreach ($collection as $entity) {
            if (!$ts) {
                $ts = $entity->getCreated();
                $previousEntity = $entity;
                continue;
            }
            $current = $entity->getCreated();
            $this->assertLessThanOrEqual($ts, $current, "Previous: " . $previousEntity->getId() . "; Current: " . $entity->getId());
            $ts = $current;
            $previousEntity = $entity;
        }

        return $collection;
    }

    public function testCanGetRecentEntries()
    {
        $date  = new \DateTime('2007-01-01');
        $item  = $this->getItem();
        $items = array();
        for ($i = 0; $i < 20; $i++) {
            $newItem       = $item;

            $newDate = clone $date;
            $newDate->add(new \DateInterval('P' . ($i * 1) . 'D'));
            $newItem['created'] = $newDate->getTimestamp();

            $id            = $newItem['id'] . '-' . $i;
            $newItem['id'] = $id;
            $items[$id]    = $newItem;
        }
        $found = $this->runQueryComparisonTests($items, 'getEntries', 2, 15);
    }

    public function testCanGetEntriesByYear()
    {
        $date  = new \DateTime('2007-01-01');
        $item  = $this->getItem();
        $items = array();
        for ($i = 0; $i < 20; $i++) {
            $newItem = $item;

            $newDate = clone $date;
            $newDate->add(new \DateInterval('P' . ($i * 7) . 'D'));
            $newItem['created'] = $newDate->getTimestamp();

            $id            = $newItem['id'] . '-' . $i;
            $newItem['id'] = $id;
            $items[$id]    = $newItem;
        }

        $found = $this->runQueryComparisonTests($items, 'getEntriesByYear', 2007, 2, 15);
    }

    public function testCanGetEntriesByMonth()
    {
        $date  = new \DateTime('2007-01-01');
        $item  = $this->getItem();
        $items = array();
        for ($i = 0; $i < 20; $i++) {
            $newItem = $item;

            $newDate = clone $date;
            $newDate->add(new \DateInterval('P' . ($i * 2) . 'D'));
            $newItem['created'] = $newDate->getTimestamp();

            $id            = $newItem['id'] . '-' . $i;
            $newItem['id'] = $id;
            $items[$id]    = $newItem;
        }

        $this->runQueryComparisonTests($items, 'getEntriesByMonth', 1, 2007, 0, 15);
    }

    public function testCanGetEntriesByDay()
    {
        $date  = new \DateTime('2007-01-01');
        $item  = $this->getItem();
        $items = array();
        for ($i = 0; $i < 20; $i++) {
            $newItem = $item;

            $newDate = clone $date;
            $newDate->add(new \DateInterval('PT' . $i . 'H'));
            $newItem['created'] = $newDate->getTimestamp();

            $id            = $newItem['id'] . '-' . $i;
            $newItem['id'] = $id;
            $items[$id]    = $newItem;
        }

        $this->runQueryComparisonTests($items, 'getEntriesByDay', 1, 1, 2007, 2, 15);
    }

    public function testCanGetEntriesByTag()
    {
        $date  = new \DateTime('2007-01-01');
        $item  = $this->getItem();
        $items = array();
        for ($i = 0; $i < 20; $i++) {
            $newItem       = $item;

            $newDate = clone $date;
            $newDate->add(new \DateInterval('P' . ($i * 1) . 'D'));
            $newItem['created'] = $newDate->getTimestamp();

            $id            = $newItem['id'] . '-' . $i;
            $newItem['id'] = $id;
            $items[$id]    = $newItem;
        }
        $this->runQueryComparisonTests($items, 'getEntriesByTag', 'foo', 2, 15);
    }
}
