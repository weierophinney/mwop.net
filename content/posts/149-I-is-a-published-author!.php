<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('149-I-is-a-published-author!');
$entry->setTitle('I is a published author!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1194370653);
$entry->setUpdated(1194370862);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'personal',
));

$body =<<<'EOT'
<p>
    So, in addition to it being my daughter's birthday, which is exciting enough
    in itself, I received a package from my publisher, 
    <a href="http://www.sitepoint.com">SitePoint</a>, with my author copies of 
    <a href="http://www.sitepoint.com/books/phpant2/">The PHP Anthology</a>.
    Very exciting to see stuff I've written published!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;