<?php
/**
 * Script for clearing caches.
 */
use Zend\Cache\Cache;

$locator = $aplication->getLocator();
$cache   = $locator->get('cache');
$cache->clean(Cache::CLEANING_MODE_ALL);
echo "Cache cleaned\n";
