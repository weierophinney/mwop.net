<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('166-Dojo-1.1.0-Released');
$entry->setTitle('Dojo 1.1.0 Released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1206734357);
$entry->setUpdated(1206734357);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    <a href="http://dojotoolkit.org/">Dojo</a> announced today the availability
    of 1.1.0. 
</p>

<p>
    I've been toying with Dojo off-and-on for almost a year now. It's the most
    framework-y of the various JS toolkits I've tried, and I particularly
    appreciate its modularity. (That said, it can lead to a lot of HTTP requests
    to your site if you don't create a targetted bundle with the modules you
    need.)
</p>

<p>
    The 1.1.0 release has me pretty excited, as it finally is doing something
    most other JS frameworks have been doing for some time: its XHR requests now
    send the "X-Requested-With: XMLHttpRequest" header, which allows it to
    conform to the <code>isXmlHttpRequest()</code> method in Zend Framework's
    request object. This makes it much easier to provide a standard mechanism in
    your server-side code for detecting AJAX requests, allowing context
    switching to be automated.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;