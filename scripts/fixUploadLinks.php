<?php
set_include_path(implode(DIRECTORY_SEPARATOR, array(
    '/home/matthew/.local/lib/php/ZendFramework/library',
    get_include_path(),
)));
putenv('APPLICATION_ENV=development');
require __DIR__ . '/../bootstrap.php';

$standardAutoloader = new Zend\Loader\StandardAutoloader(array(
    'prefixes' => array(
        'Zend_' => '/home/matthew/.local/lib/php/ZendFramework/library/Zend',
    )
));
$standardAutoloader->register();

// $application is already defined at this time as a Zf2Mvc\Application 
// instance. We'll now pull the locator from it.
$locator = $application->getLocator();

// Ensure we have a clean DB
$mongo  = $locator->get('Mongo');
$dbs    = $mongo->listDBs();
$hasDb  = array_reduce($dbs['databases'], function($has, $current) {
    if (!is_array($current) || !isset($current['name'])) {
        return $has || false;
    }

    if ($current['name'] == 'mwoptest') {
        return true;
    }

    return $has || false;
}, false);
if (!$hasDb) {
    echo "Missing db!\n";
    var_export($dbs['databases']);
    exit(1);
}

$resource = $locator->get('Blog\EntryResource');
foreach ($resource->getAll() as $entry) {
    $changed  = false;
    $content  = $entry->getBody();
    $extended = $entry->getExtended();
    if (strstr($content, 'matthew/uploads/')) {
        $content = str_replace('matthew/uploads/', 'uploads/', $content);
        $entry->setBody($content);
        $changed = true;
    }
    if (strstr($content, 'matthew/uploads/')) {
        $extended = str_replace('matthew/uploads/', 'uploads/', $extended);
        $entry->setExtended($extended);
        $changed = true;
    }

    if ($changed) {
        printf("Updating entry (%s) - %s\n", $entry->getId(), $entry->getTitle());
        $resource->update($entry->getId(), $entry);
    }
}
