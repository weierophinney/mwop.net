<?php
/**
 * Script for clearing caches.
 */
use Zend\Cache\Storage\Adapter as CacheAdapter;

$locator  = $aplication->getLocator();
$listener = $locator->get('Cache\Listener');
$cache    = $listener->getCache();
$cache->clear(CacheAdapter::MATCH_ALL);
echo "Cache cleaned\n";
