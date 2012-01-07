<?php
/**
 * Script for clearing caches.
 */
use Zend\Cache\Cache;

$locator  = $aplication->getLocator();
$listener = $locator->get('Cache\Listener');
$cache    = $listener->getCache();
$cache->clean(Cache::CLEANING_MODE_ALL);
echo "Cache cleaned\n";
