<?php
set_include_path(implode(DIRECTORY_SEPARATOR, array(
    '/home/matthew/.local/lib/php/ZendFramework/library',
    get_include_path(),
)));
putenv('APPLICATION_ENV=testing');
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

    if ($current['name'] == 'importtest') {
        return true;
    }

    return $has || false;
}, false);
if ($hasDb) {
    $mongo->dropDB('importtest');
}

// Initialize objects used throughout script
$mongodb  = $locator->get('MongoDB');
$resource = $locator->get('Blog\EntryResource');

$db = Zend_Db::factory('mysqli', array(
    'host'     => 'localhost',
    'username' => 'root',
    'password' => 'd@rw1n',
    'dbname'   => 'wop_serendipity',
));

// Transfer users
/* Commenting until we have a user entity
$select = $db->select()
        ->from('serendipity_authors');
$rows   = $db->fetchAll($select);
$users  = array();
echo "Preparing users to save";
foreach ($rows as $row) {
    $user = new Entity\User();
    $user->setId($row['username'])
         ->setRealname($row['realname'])
         ->setEmail($row['email'])
         ->setPassword($row['password'])
         ->setMailComments($row['mail_comments'])
         ->setMailTrackbacks($row['mail_trackbacks']);
    $users[] = $user->toArray();
    echo '    ' . $user->getId() . "\n";
}
$mongodb->createCollection('users')->batchInsert($users);
echo "Saved users to database\n";
 */

// Get entries
$select  = $db->select()
         ->from('serendipity_entries');
$rows    = $db->fetchAll($select);
$entries = array();
echo "Preparing entries to save (" . count($rows) . " rows)\n";
foreach ($rows as $row) {
    echo "    Examining " . $row['id'] . "\n";
    echo "        Getting permalink... ";
    $select = $db->select()
            ->from('serendipity_permalinks', array('permalink'))
            ->where('entry_id = ?', $row['id'])
            ->where('type = ?', 'entry');
    $permalink = $db->fetchOne($select);
    $permalink = preg_replace('#^archives/(\d+-.*?)\.html$#', '$1', $permalink);
    echo $permalink, "\n";

    $body = preg_replace('#\[geshi( lang=([^\]])*)?\](.*?)(\[/geshi\])#se', "'<div class=\"example\"><pre><code\\1>'.htmlspecialchars('\\3', ENT_COMPAT, 'UTF-8').'</code></pre></div>'", $row['body']);
    $body = preg_replace('/lang=([^" >]*)/s', 'lang="$1"', $body);
    $body = str_replace('matthew/uploads/', 'uploads/', $body);
    $body = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $body);

    $extended = preg_replace('#\[geshi( lang=([^\]])*)?\](.*?)(\[/geshi\])#se', "'<div class=\"example\"><pre><code\\1>'.htmlspecialchars('\\3', ENT_COMPAT, 'UTF-8').'</code></pre></div>'", $row['extended']);
    $extended = preg_replace('/lang=([^" >]*)/s', 'lang="$1"', $extended);
    $extended = str_replace('matthew/uploads/', 'uploads/', $extended);
    $extended = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $extended);

    echo "        Creating entry entity... ";
    $entry = array(
        'id'        => $permalink,
        'title'     => $row['title'],
        'created'   => $row['timestamp'],
        'updated'   => $row['last_modified'],
        'author'    => 'matthew',
        'body'      => $body,
        'extended'  => $extended,
        'is_draft'  => ($row['isdraft'] == 'true' ? true : false),
        'is_public' => true,
        'version'   => 1,
    );

    echo "        Getting entry properties... ";
    $propSelect = $db->select()
                ->from('serendipity_entryproperties')
                ->where('entryid = ?', $row['id']);
    $entry['metadata'] = array();
    $metadata = $db->fetchAll($propSelect);
    foreach ($metadata as $datum) {
        $entry['metadata'][$datum['property']] = $datum['value'];
    }
    if (isset($entry['metadata']['ep_access'])) {
        if ('private' == $entry['metadata']['ep_access']) {
            $entry['is_public'] = false;
        }
    }
    echo "[DONE]\n";

    echo "        Getting entry categories... ";
    $catSelect = $db->select()
               ->from(array('c' => 'serendipity_category'), array('category_name'))
               ->joinInner(array('ec' => 'serendipity_entrycat'), 'ec.categoryid = c.categoryid', array())
               ->where('ec.entryid = ?', $row['id']);
    $tags = $db->fetchCol($catSelect);
    echo "[DONE]\n";

    echo "        Getting entry tags... ";
    $tagSelect = $db->select()
               ->from('serendipity_entrytags', array('tag'))
               ->where('entryid = ?', $row['id']);
    $tags = $tags + $db->fetchCol($tagSelect);

    $tags = array_unique($tags);
    $tags = array_map(function($tag) {
        return strtolower($tag);
    }, $tags);
    $entry['tags'] = $tags;
    echo "[DONE]\n";

    echo "        Saving entry... ";
    try {
        $return = $resource->create($entry);
        echo $return->getId() . "\n";
    } catch (Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n";
    }
}
echo "Saved entries to database\n";

// Get comments
$select   = $db->select()
          ->from('serendipity_comments')
          ->where('status = ?', 'approved');
$rows     = $db->fetchAll($select);
$comments = array();
echo "Preparing comments to save";
foreach ($rows as $row) {
    $select = $db->select()
            ->from('serendipity_permalinks', array('permalink'))
            ->where('entry_id = ?', $row['entry_id'])
            ->where('type = ?', 'entry');
    $permalink = $db->fetchOne($select);
    $permalink = preg_replace('#^archives/(\d+-.*?)\.html$#', '$1', $permalink);

    // Ensure content is UTF-8
    foreach ($row as $key => $value) {
        $row[$key] = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $value);
    }

    if (!isset($comments[$permalink])) {
        $comments[$permalink] = array($row);
    } else {
        $comments[$permalink][] = $row;
    }

}
foreach ($comments as $permalink => $collection) {
echo "    Saving comments for entry '$permalink'... ";
    $entry = $resource->get($permalink);
    if (!$entry instanceof mwop\Entity\Entry) {
echo "Entry does not exist; skipping\n";
        continue;
    }
    $entry->setComments($collection);
    try {
        $resource->update($permalink, $entry);
echo "[SAVED]\n";
    } catch (Exception $e) {
echo "FAILURE: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    }
}
echo "Saved comments to database\n";

// Inserting uploaded files
// Do this later, once entities exist
/*
$select = $db->select()
        ->from('serendipity_images');
$rows   = $db->fetchAll($select);
$props  = array(
    'size',
    'dimensions_width',
    'dimensions_height',
    'thumbnail_name',
);
$files  = array();
echo "Preparing files to save";
foreach ($rows as $row) {
    $file = new Entity\File();
    $file->setMime($row['mime'])
         ->setTimestamp($row['date'])
         ->setAuthor('matthew')
         ->setId($row['name'] . '.' . $row['extension']);
    foreach ($props as $prop) {
        if (!empty($row[$prop])) {
            $file->setProperty($prop, $row[$prop]);
        }
    }
    $files[] = $file->toArray();
    echo '    ' . $file->getId() . "\n";
}
$mongodb->createCollection('files')->batchInsert($files);
echo "Saved files to database\n";
 */
