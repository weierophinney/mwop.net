<?php
namespace Mwop\Github;

use Zend\Feed\Reader\ExtensionManagerInterface;

class AtomReaderExtensions implements ExtensionManagerInterface
{
    protected $factories = [
        'atom\entry'            => 'Zend\Feed\Reader\Extension\Atom\Entry',
        'atom\feed'             => 'Zend\Feed\Reader\Extension\Atom\Feed',
        'content\entry'         => 'Zend\Feed\Reader\Extension\Content\Entry',
        'creativecommons\entry' => 'Zend\Feed\Reader\Extension\CreativeCommons\Entry',
        'creativecommons\feed'  => 'Zend\Feed\Reader\Extension\CreativeCommons\Feed',
        'dublincore\entry'      => 'Zend\Feed\Reader\Extension\DublinCore\Entry',
        'dublincore\feed'       => 'Zend\Feed\Reader\Extension\DublinCore\Feed',
        'podcast\entry'         => 'Zend\Feed\Reader\Extension\Podcast\Entry',
        'podcast\feed'          => 'Zend\Feed\Reader\Extension\Podcast\Feed',
        'slash\entry'           => 'Zend\Feed\Reader\Extension\Slash\Entry',
        'syndication\feed'      => 'Zend\Feed\Reader\Extension\Syndication\Feed',
        'thread\entry'          => 'Zend\Feed\Reader\Extension\Thread\Entry',
        'wellformedweb\entry'   => 'Zend\Feed\Reader\Extension\WellFormedWeb\Entry',
    ];

    public function has($name)
    {
        return array_key_exists(strtolower($name), $this->factories);
    }

    public function get($name)
    {
        $name = strtolower($name);

        if (! isset($this->factories[$name])) {
            throw new RuntimeException(sprintf('No service defined by name "%s"', $name));
        }

        $factory = $this->factories[$name];

        return new $factory();
    }
}
