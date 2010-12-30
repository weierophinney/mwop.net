<?php
namespace mwop\Resource;

use PHPUnit_Framework_TestCase as TestCase;

class CollectionTest extends TestCase
{
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

    public function testCanIterateCollection()
    {
        $items      = $this->getItems();
        $collection = new Collection($items, 'mwop\Entity\Entry');
        $i          = 0;
        foreach ($collection as $item) {
            $i++;
        }
        $this->assertTrue($i > 0);
    }

    public function testCollectionIsCountable()
    {
        $items = $this->getItems();
        $collection = new Collection($items, 'mwop\Entity\Entry');
        $this->assertEquals(count($items), count($collection));
    }

    public function testIteratingOverCollectionReturnsObjectsOfSpecifiedClass()
    {
        $items      = $this->getItems();
        $collection = new Collection($items, 'mwop\Entity\Entry');
        $i          = 0;
        foreach ($collection as $item) {
            $this->assertInstanceOf('mwop\Entity\Entry', $item);
        }
    }
}
