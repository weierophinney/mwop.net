<?php
/**
 * This script is intended for exporting comments from s9y to Disqus. Disqus can
 * import comments from a WXR (WordPress eXtended RSS) feed, which this script
 * produces.
 *
 * For more information: http://docs.disqus.com/developers/export/import_format/
 */

$baseUrl = 'http://mwop.net/blog/';

set_include_path(implode(DIRECTORY_SEPARATOR, array(
    '/home/matthew/git/zf2/library',
    get_include_path(),
)));

require_once 'Zend/Loader/StandardAutoloader.php';
$standardAutoloader = new Zend\Loader\StandardAutoloader(array(
    'namespaces' => array(
        'Wxr\Helper' => __DIR__ . '/helper',
    ),
));
$standardAutoloader->register();

$db = Zend\Db\Db::factory('Mysqli', array(
    'host'     => 'localhost',
    'username' => 'root',
    'password' => 'd@rw1n',
    'dbname'   => 'wop_serendipity',
));

// Get entries with comments
$select  = $db->select()
              ->from('serendipity_entries', array('id', 'title', 'body', 'allow_comments', 'timestamp'))
              ->where('comments > ?', 0);
$rows    = $db->fetchAll($select);
$entries = array();
echo "Retrieving and looping over all entries with comments\n";
foreach ($rows as $row) {
    echo "    Examining " . $row['id'] . "\n";
    echo "        Getting permalink... ";
    $select = $db->select()
            ->from('serendipity_permalinks', array('permalink'))
            ->where('entry_id = ?', $row['id'])
            ->where('type = ?', 'entry');
    $identifier = $db->fetchOne($select);
    $identifier = preg_replace('#^archives/(\d+-.*?)\.html$#', '$1', $identifier);
    $permalink  = $baseUrl . $identifier;
    echo $permalink, "\n";

    echo "        Converting body to UTF-8... ";
    $body = preg_replace('#\[geshi( lang=([^\]])*)?\](.*?)(\[/geshi\])#se', "'<div class=\"example\"><pre><code\\1>'.htmlspecialchars('\\3', ENT_COMPAT, 'UTF-8').'</code></pre></div>'", $row['body']);
    $body = preg_replace('/lang=([^" >]*)/s', 'lang="$1"', $body);
    $body = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $body);
    echo "[DONE]\n";

    // Get comments
    $select      = $db->select()
                      ->from('serendipity_comments')
                      ->where('entry_id = ?', $row['id'])
                      ->where('status = ?', 'approved');
    $commentRows = $db->fetchAll($select);
    $comments    = array();
    echo "        Preparing comments to save...";
    foreach ($commentRows as $comment) {
        // Ensure content is UTF-8
        foreach ($comment as $key => $value) {
            $comment[$key] = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $value);
        }
        $comments[] = $comment;
    }
    echo "[DONE]\n";

    $entries[] = array(
        'title'          => $row['title'],
        'link'           => $permalink,
        'identifier'     => $identifier,
        'body'           => $body,
        'date'           => $row['timestamp'],
        'allow_comments' => $row['allow_comments'],
        'comments'       => $comments,
    );
}
echo "[DONE] Retrieving and looping over all entries with comments\n";

echo "Rendering WXR feed...";
$view = new Zend\View\PhpRenderer();
$view->resolver()->addPath(__DIR__ . '/template');
$view->getBroker()->register('gmtDate', new Wxr\Helper\GmtDate());
$view->entries = $entries;
$xml = $view->render('comments_wxr.xml');
file_put_contents(__DIR__ . '/../data/comments_wxr.xml', $xml);
echo "[DONE]\n";
