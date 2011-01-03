<?php
namespace mwop\Resource;

use PHPUnit_Framework_TestCase as TestCase,
    mwop\DataSource\Mock as MockDataSource,
    mwop\DataSource\Query,
    mwop\Entity\Entry,
    Zend\SignalSlot\Signals;

class EntryResourceTest extends TestCase
{
    public function setUp()
    {
        EntryResource::resetSignals();
        $this->dataSource = new MockDataSource();
        $this->resource   = new EntryResource();
        $this->resource->setDataSource($this->dataSource);
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

    public function testSignalsArePopulatedByDefault()
    {
        $signals = EntryResource::signals();
        $this->assertInstanceOf('Zend\SignalSlot\SignalSlot', $signals);
    }

    public function testResettingSignalsCreatesNewSignalsObject()
    {
        $signals = EntryResource::signals();
        EntryResource::resetSignals();
        $test = EntryResource::signals();
        $this->assertNotSame($signals, $test);
    }

    public function testCanInjectSignalsObject()
    {
        $signals = new Signals();
        EntryResource::signals($signals);
        $test = EntryResource::signals();
        $this->assertSame($signals, $test);
    }

    public function testIsAclResource()
    {
        $this->assertInstanceOf('Zend\Acl\Resource', $this->resource);
    }

    public function testUsesClassNameAsAclResourceIdentifier()
    {
        $this->assertEquals(get_class($this->resource), $this->resource->getResourceId());
    }

    public function testGetAllReturnsCollection()
    {
        $test = $this->resource->getAll();
        $this->assertInstanceOf('mwop\Resource\Collection', $test);
    }

    public function testSignalHandlerCanShortCircuitGetAllExecutionIfItReturnsACollection()
    {
        $items = $this->getItems();
        $this->dataSource->when(new Query(), $items);
        $item  = $this->getItem();
        $collection = new Collection(array($item), 'mwop\Entity\Entry');
        EntryResource::signals()->connect('get-all.pre', function($resource) use ($collection) {
            return $collection;
        });
        $test = $this->resource->getAll();
        $this->assertSame($collection, $test);
        $this->assertNotEquals(count($items), count($test));
    }

    public function testGetAllTriggersPostActionWithItemsFound()
    {
        $items  = $this->getItems();
        $this->dataSource->when(new Query(), $items);

        $result = new \stdClass();
        EntryResource::signals()->connect('get-all.post', function($items) use ($result) {
            $result->items = $items;
        });

        $collection = $this->resource->getAll();
        $this->assertObjectHasAttribute('items', $result);
        $this->assertSame($collection, $result->items);
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

    public function testSignalHandlerCanShortCircuitGetExecutionIfItReturnsAnEntry()
    {
        $item  = $this->getItem();
        $entry = $this->resource->create($item);

        $test  = new Entry();
        EntryResource::signals()->connect('get.pre', function ($id, $resource) use ($test) {
            return $test;
        });
        $receive = $this->resource->get($entry->getId());
        $this->assertSame($test, $receive);
    }

    public function testPostGetSignalTriggeredWithEntry()
    {
        $item  = $this->getItem();
        $entry = $this->resource->create($item);
        $test  = new Entry();
        EntryResource::signals()->connect('get.post', function ($entry, $resource) use ($test) {
            $test->fromArray($entry->toArray());
        });
        $receive = $this->resource->get($entry->getId());
        $this->assertSame($receive->toArray(), $test->toArray());
    }

    public function testPostGetSignalNotTriggeredIfEntryNotFound()
    {
        $test  = new \stdClass;
        EntryResource::signals()->connect('get.post', function ($entry, $resource) use ($test) {
            $test->entry = $entry;
        });
        $this->assertNull($this->resource->get('foo-bar'));
        $this->assertFalse(isset($test->entry));
    }

    public function testCreateReturnsEntryWithValidArrayData()
    {
        $item  = $this->getItem();
        $entry = $this->resource->create($item);
        $this->assertInstanceOf('mwop\Entity\Entry', $entry);
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

    public function testCreateTriggersPreCreateSignal()
    {
        $o = new \stdClass();
        EntryResource::signals()->connect('create.pre', function ($entry) use ($o) {
            $o->entry = $entry;
        });
        $entry = $this->resource->create($this->getItem());
        $this->assertObjectHasAttribute('entry', $o);
        $this->assertSame($entry, $o->entry);
    }

    public function testCreateTriggersPostCreateSignal()
    {
        $o = new \stdClass();
        EntryResource::signals()->connect('create.post', function ($entry) use ($o) {
            $o->entry = $entry;
        });
        $entry = $this->resource->create($this->getItem());
        $this->assertObjectHasAttribute('entry', $o);
        $this->assertSame($entry, $o->entry);
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

    public function testUpdateTriggersPreUpdateSignal()
    {
        EntryResource::signals()->connect('update.pre', function($id, $spec) {
            throw new \Exception(sprintf(
                'Received id "%s" and spec "%s"',
                $id,
                implode(':', $spec->getArrayCopy())
            ));
        });
        $item = $this->getItem();
        $this->dataSource->create($item);
        $this->setExpectedException('Exception', 'id "some-slug" and spec "some title"');
        $this->resource->update('some-slug', array('title' => 'some title'));
    }

    public function testPreUpdateSignalReceivesReferenceToSpecArray()
    {
        $item = $this->getItem();
        $this->dataSource->create($item);

        EntryResource::signals()->connect('update.pre', function($id, $spec) {
            $spec['updated'] = strtotime('tomorrow');
        });

        $entry    = $this->resource->update('some-slug', array('title' => 'some title'));
        $expected = strtotime('tomorrow');
        $this->assertEquals($expected, $entry->getUpdated());
    }

    public function testPostUpdateSignalsAreTriggered()
    {
        $o = new \stdClass;
        EntryResource::signals()->connect('update.post', function($entry) use ($o) {
            $o->entry = $entry;
        });

        $item = $this->getItem();
        $this->dataSource->create($item);
        $entry = $this->resource->update('some-slug', array('title' => 'some title'));

        $this->assertObjectHasAttribute('entry', $o);
        $this->assertSame($entry, $o->entry);
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

    public function testDeleteTriggersPreDeleteSignal()
    {
        $item  = $this->getItem();
        $entry = $this->resource->create($item);
        $test  = new \stdClass();
        EntryResource::signals()->connect('delete.pre', function($entry, $resource) use ($test) {
            $test->triggered = true;
        });
        $this->resource->delete($item['id']);
        $this->assertObjectHasAttribute('triggered', $test);
    }

    public function testPreDeleteSignalHandlerCanCircumventDeletion()
    {
        $item  = $this->getItem();
        $entry = $this->resource->create($item);
        EntryResource::signals()->connect('delete.pre', function($entry, $resource) {
            return true;
        });
        $this->assertTrue($this->resource->delete($item['id']));
        $test = $this->resource->get($item['id']);
        $this->assertInstanceOf('mwop\Entity\Entry', $test);
        $this->assertEquals($entry->toArray(), $test->toArray());
    }

    public function testPostDeleteSignalIsCalled()
    {
        $item  = $this->getItem();
        $entry = $this->resource->create($item);
        $test  = new \stdClass;
        EntryResource::signals()->connect('delete.post', function($id) use ($test) {
            $test->id = $id;
        });
        $this->assertTrue($this->resource->delete($item['id']));
        $this->assertObjectHasAttribute('id', $test);
        $this->assertEquals($item['id'], $test->id);
    }
}
