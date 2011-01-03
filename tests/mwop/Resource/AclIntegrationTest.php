<?php
namespace mwop\Resource;

use mwop\Acl,
    mwop\DataSource\Mock as MockDataSource,
    mwop\Entity\Entry,
    Zend\Acl\Role\GenericRole as Role,
    PHPUnit_Framework_TestCase as TestCase;

class AclIntegrationTest extends TestCase
{
    public function setUp()
    {
        EntryResource::resetSignals();
        $this->dataSource = new MockDataSource();
        $this->resource   = new EntryResource();
        $this->resource->setDataSource($this->dataSource);
    }

    public function setupUnauthorizedUser()
    {
        $this->acl  = new Acl();
        $this->role = new Role('matthew');
        $this->acl->addRole($this->role);
        $this->handler = new Acl\SignalHandler($this->role, $this->acl);
    }

    public function setupAuthorizedUser()
    {
        $this->acl  = new Acl();
        $this->role = new Role('admin');
        $this->handler = new Acl\SignalHandler($this->role, $this->acl);
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

    /* invalid user */

    public function testAclsAbortGetAllForUnauthorizedUser()
    {
        $this->setupUnauthorizedUser();
        EntryResource::signals()->connect('get-all.pre', $this->handler, 'verifyRead');
        $this->setExpectedException('mwop\Acl\AclException');
        $this->resource->getAll();
    }

    public function testAclsAbortGetForUnauthorizedUser()
    {
        $this->setupUnauthorizedUser();
        EntryResource::signals()->connect('get.pre', $this->handler, 'verifyRead');
        $this->setExpectedException('mwop\Acl\AclException');
        $this->resource->get('foo');
    }

    public function testAclsAbortCreateForUnauthorizedUser()
    {
        $this->setupUnauthorizedUser();
        EntryResource::signals()->connect('create.pre', $this->handler, 'verifyWrite');
        $this->setExpectedException('mwop\Acl\AclException');
        $this->resource->create($this->getItem());
    }

    public function testAclsAbortUpdateForUnauthorizedUser()
    {
        $this->setupUnauthorizedUser();
        $item = $this->getItem();
        $this->dataSource->create($item);

        EntryResource::signals()->connect('update.pre', $this->handler, 'verifyWrite');
        $this->setExpectedException('mwop\Acl\AclException');
        $this->resource->update($item['id'], array('title' => 'new title'));
    }

    public function testAclsAbortDeleteForUnauthorizedUser()
    {
        $this->setupUnauthorizedUser();
        $item = $this->getItem();
        $this->dataSource->create($item);

        EntryResource::signals()->connect('delete.pre', $this->handler, 'verifyWrite');
        $this->setExpectedException('mwop\Acl\AclException');
        $this->resource->delete($item['id']);
    }

    /* valid user */

    public function testAuthorizedUserMayGetAll()
    {
        $this->setupAuthorizedUser();
        EntryResource::signals()->connect('get-all.pre', $this->handler, 'verifyRead');
        $result = $this->resource->getAll();
        $this->assertInstanceOf('mwop\Resource\Collection', $result);
    }

    public function testAuthorizedUserMayGetOne()
    {
        $this->setupAuthorizedUser();
        $item = $this->getItem();
        $this->resource->create($item);
        EntryResource::signals()->connect('get.pre', $this->handler, 'verifyRead');
        $entry = $this->resource->get($item['id']);
        $this->assertInstanceOf('mwop\Entity\Entry', $entry);
    }

    public function testAuthorizedUserMayCreate()
    {
        $this->setupAuthorizedUser();
        $item = $this->getItem();
        EntryResource::signals()->connect('create.pre', $this->handler, 'verifyWrite');
        $entry = $this->resource->create($item);
        $this->assertInstanceOf('mwop\Entity\Entry', $entry);
    }

    public function testAuthorizedUserMayUpdate()
    {
        $this->setupAuthorizedUser();
        $item = $this->getItem();
        $this->dataSource->create($item);

        EntryResource::signals()->connect('update.pre', $this->handler, 'verifyWrite');
        $entry = $this->resource->update($item['id'], array('title' => 'new title'));
        $this->assertInstanceOf('mwop\Entity\Entry', $entry);
        $this->assertEquals('new title', $entry->getTitle());
    }

    public function testAuthorizedUserMayDelete()
    {
        $this->setupAuthorizedUser();
        $item = $this->getItem();
        $this->dataSource->create($item);

        EntryResource::signals()->connect('delete.pre', $this->handler, 'verifyWrite');
        $this->resource->delete($item['id']);
        $this->assertNull($this->resource->get($item['id']));
    }
}
