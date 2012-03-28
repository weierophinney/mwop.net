<?php

namespace PhlyBlog;

use PHPUnit_Framework_TestCase as TestCase,
    DateTime;

class EntryEntityTest extends TestCase
{
    public function setUp()
    {
        $this->entry = new EntryEntity();
    }

    public function testUsesEntryFilterAsDefaultFilter()
    {
        $filter = $this->entry->getInputFilter();
        $this->assertInstanceOf('PhlyBlog\Filter\EntryFilter', $filter);
    }

    public function testSettingTitleSetsId()
    {
        $this->entry->setTitle('Foo Bar');
        $this->assertEquals('foo-bar', $this->entry->getId());
    }

    public function testAcceptsStringsForCreatedTimestamps()
    {
        $this->entry->setCreated('today');
        $expected = strtotime('today');
        $this->assertEquals($expected, $this->entry->getCreated());
    }

    public function testAcceptsIntegersForCreatedTimestamps()
    {
        $expected = strtotime('2010-12-29 15:39Z-0500');
        $this->entry->setCreated($expected);
        $this->assertEquals($expected, $this->entry->getCreated());
    }

    public function testAcceptsDateTimeForCreatedTimestamps()
    {
        $date = new DateTime('today');
        $this->entry->setCreated($date);
        $this->assertEquals($date->getTimestamp(), $this->entry->getCreated());
    }

    public function testAcceptsStringsForUpdatedTimestamps()
    {
        $this->entry->setUpdated('today');
        $expected = strtotime('today');
        $this->assertEquals($expected, $this->entry->getUpdated());
    }

    public function testAcceptsIntegersForUpdatedTimestamps()
    {
        $expected = strtotime('2010-12-29 15:39Z-0500');
        $this->entry->setUpdated($expected);
        $this->assertEquals($expected, $this->entry->getUpdated());
    }

    public function testAcceptsDateTimeForUpdatedTimestamps()
    {
        $date = new DateTime('today');
        $this->entry->setUpdated($date);
        $this->assertEquals($date->getTimestamp(), $this->entry->getUpdated());
    }

    public function testAmericaNewYorkIsDefaultTimezone()
    {
        $this->assertEquals('America/New_York', $this->entry->getTimezone());
    }

    public function testIsDraftByDefault()
    {
        $this->assertTrue($this->entry->isDraft());
    }

    public function testIsPublicByDefault()
    {
        $this->assertTrue($this->entry->isPublic());
    }

    public function testNoTagsByDefault()
    {
        $this->assertEquals(array(), $this->entry->getTags());
    }

    public function testCanAddManyTagsAtOnce()
    {
        $this->entry->setTags(array('foo', 'bar', 'baz'));
        $this->assertEquals(array('foo', 'bar', 'baz'), $this->entry->getTags());
    }

    public function testCallingSetTagsMultipleTimesOverwrites()
    {
        $this->entry->setTags(array('foo', 'bar', 'baz'));
        $this->entry->setTags(array('oof', 'rab', 'zab'));
        $this->assertEquals(array('oof', 'rab', 'zab'), $this->entry->getTags());
    }

    public function testCanAddTagsOneAtATime()
    {
        $this->entry->setTags(array('foo'))
                    ->addTag('baz')
                    ->addTag('bar');
        $this->assertEquals(array('foo', 'baz', 'bar'), $this->entry->getTags());
    }

    public function testCanRemoveSingleTags()
    {
        $this->entry->setTags(array('foo', 'bar', 'baz'));
        $this->entry->removeTag('bar');
        $this->assertEquals(array('foo', 'baz'), array_values($this->entry->getTags()));
    }

    public function testCanPopulateFromArray()
    {
        $this->loadFromArray();
        $this->assertEquals('foo-bar', $this->entry->getId());
        $this->assertEquals('Foo Bar', $this->entry->getTitle());
        $this->assertEquals('Foo bar. Baz. Bat bedat.', $this->entry->getBody());
        $this->assertEquals('matthew', $this->entry->getAuthor());
        $this->assertTrue($this->entry->isDraft());
        $this->assertFalse($this->entry->isPublic());
        $this->assertEquals(strtotime('today'), $this->entry->getCreated());
        $this->assertEquals(strtotime('today'), $this->entry->getUpdated());
        $this->assertEquals('America/Chicago', $this->entry->getTimezone());
        $this->assertEquals(array('foo', 'bar'), $this->entry->getTags());
    }

    public function testCanSerializeToArray()
    {
        $this->loadFromArray();
        $values = $this->entry->toArray();
        $expected = array(
            'id'        => 'foo-bar',
            'title'     => 'Foo Bar',
            'body'      => 'Foo bar. Baz. Bat bedat.',
            'author'    => 'matthew',
            'is_draft'  => true,
            'is_public' => false,
            'created'   => strtotime('today'),
            'updated'   => strtotime('today'),
            'timezone'  => 'America/Chicago',
            'tags'      => array('foo', 'bar'),
        );
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $values[$key]);
        }
    }

    public function testOverloadingOfProperties()
    {
        $this->loadFromArray();
        $this->assertTrue(isset($this->entry->id));
        $this->assertTrue(isset($this->entry->title));
        $this->assertTrue(isset($this->entry->body));
        $this->assertTrue(isset($this->entry->author));
        $this->assertTrue(isset($this->entry->isDraft));
        $this->assertTrue(isset($this->entry->isPublic));
        $this->assertTrue(isset($this->entry->created));
        $this->assertTrue(isset($this->entry->updated));
        $this->assertTrue(isset($this->entry->timezone));
        $this->assertTrue(isset($this->entry->tags));
        $this->assertEquals('foo-bar', $this->entry->id);
        $this->assertEquals('Foo Bar', $this->entry->title);
        $this->assertEquals('Foo bar. Baz. Bat bedat.', $this->entry->body);
        $this->assertEquals('matthew', $this->entry->author);
        $this->assertTrue($this->entry->isDraft);
        $this->assertFalse($this->entry->isPublic);
        $this->assertEquals(strtotime('today'), $this->entry->created);
        $this->assertEquals(strtotime('today'), $this->entry->updated);
        $this->assertEquals('America/Chicago', $this->entry->timezone);
        $this->assertEquals(array('foo', 'bar'), $this->entry->tags);
    }

    public function testValidationFailsInitially()
    {
        $this->assertFalse($this->entry->isValid());
    }

    public function testValidEntryValidates()
    {
        $this->loadFromArray();
        $valid    = $this->entry->isValid();
        $messages = $this->entry->getInputFilter()->getMessages();
        $this->assertTrue($valid, var_export($messages, 1));
    }

    public function testInputFilterOverwritesValuesWithFilteredVersions()
    {
        $this->loadFromArray();
        $this->entry->setTitle('foo & bar')
                    ->setId('foo-bar')
                    ->setDraft(0)
                    ->setPublic('')
                    ->setBody('  Foo Bar. ')
                    ->setAuthor(' matthew ');
        $this->assertTrue($this->entry->isValid());
        $this->assertEquals('foo &amp; bar', $this->entry->getTitle());
        $this->assertFalse($this->entry->isDraft());
        $this->assertFalse($this->entry->isPublic());
        $this->assertEquals('Foo Bar.', $this->entry->getBody());
        $this->assertEquals('matthew', $this->entry->getAuthor());
    }

    public function testVersionIs2ByDefault()
    {
        $this->assertEquals(2, $this->entry->getVersion());
    }

    public function testSerializingVersion1EntryIncludesComments()
    {
        $this->loadFromArray();
        $this->entry->setVersion(1);
        $data = $this->entry->toArray();
        $this->assertArrayHasKey('comments', $data);
        $this->assertEquals(2, count($data['comments']));
        foreach ($data['comments'] as $comment) {
            $this->assertInternalType('array', $comment);
        }
    }

    public function loadFromArray()
    {
        $this->entry->fromArray(array(
            'id'        => 'foo-bar',
            'title'     => 'Foo Bar',
            'body'      => 'Foo bar. Baz. Bat bedat.',
            'author'    => 'matthew',
            'is_draft'  => true,
            'is_public' => false,
            'created'   => strtotime('today'),
            'updated'   => strtotime('today'),
            'timezone'  => 'America/Chicago',
            'tags'      => array('foo', 'bar'),
            'comments'  => array(
                array(
                    'created'  => strtotime('today'),
                    'timezone' => 'America/Chicago',
                    'title'    => 'comment',
                    'author'   => 'somebody',
                    'type'     => 'comment',
                ),
                array(
                    'created'  => strtotime('today'),
                    'timezone' => 'America/Chicago',
                    'title'    => 'trackback',
                    'type'     => 'trackback',
                    'url'      => 'http://example.com/foo',
                ),
            )
        ));
    }
}
