<?php
namespace CommonResource;

class Module
{
    public function init()
    {
        $this->initAutoloader();
    }

    public function initAutoloader()
    {
        include __DIR__ . '/autoload_register.php';
    }

    public function getDependencies()
    {
        return array(
            'php' => array(
                'required' => true,
                'version'  => '>=5.3.1',
            ),
            'ext/mongo' => array(
                'required' => true,
                'version'  => '>=1.2.0',
            ),
            'zf2' => array(
                'required' => false,
                'version'  => '>=2.0.0dev4',
            ),
            'Zend_Acl' => array(
                'required' => true,
                'version'  => '>=2.0.0dev4',
            ),
            'Zend_EventManager' => array(
                'required' => true,
                'version'  => '>=2.0.0dev4',
            ),
            'Zend_Filter' => array(
                'required' => true,
                'version'  => '>=2.0.0dev4',
            ),
            'Zend_Validator' => array(
                'required' => true,
                'version'  => '>=2.0.0dev4',
            ),
        );
    }
}
