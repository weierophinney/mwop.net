<?php
namespace mwop;

use Zend\Acl\Acl as AclDefinition;

class Acl extends AclDefinition
{
    public function __construct()
    {
        $this->addRole('guest')
             ->addRole('admin', 'guest');

        $this->addResource('mwop\Resource\EntryResource');

        $this->allow('guest', 'mwop\Resource\EntryResource', array('read'))
             ->allow('admin', 'mwop\Resource\EntryResource', array('write'));
    }
}
