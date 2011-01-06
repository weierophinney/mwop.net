<?php
namespace mwop\Acl;

use mwop\Acl,
    Zend\Acl\Acl as AclDefinition,
    Zend\Acl\Role;

class SignalHandler
{
    protected $acl;
    protected $role;

    public function __construct(Role $role, AclDefinition $acl = null)
    {
        $this->role = $role;

        if (null === $acl) {
            $acl = new Acl();
        }
        $this->acl = $acl;
    }

    public function verifyRead($resource)
    {
        if (!$this->acl->isAllowed($this->role, $resource, 'read')) {
            throw new AclException('Current user is not authorized to read the requested resource');
        }
    }

    public function verifyReadUser($resource)
    {
        if (!$this->acl->isAllowed($this->role, $resource, 'read-user')) {
            throw new AclException('Current user is not authorized to read the requested resource');
        }
    }

    public function verifyWrite($resource)
    {
        if (!$this->acl->isAllowed($this->role, $resource, 'write')) {
            throw new AclException('Current user is not authorized to modify the requested resource');
        }
    }
}
