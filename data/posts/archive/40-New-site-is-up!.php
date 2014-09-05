<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('40-New-site-is-up!');
$entry->setTitle('New site is up!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1095728972);
$entry->setUpdated(1095729137);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'personal',
  2 => 'php',
));

$body =<<<'EOT'
<p>The new weierophinney.net/matthew/ site is now up and running!</p>
<p>
    The site has been many months in planning, and about a month or so in actual
    coding. I have written the site in, instead of flatfiles, PHP, so as to:
</p>
<ul>
    <li>Allow easier updating (it includes its own content management system</li>
    <li>Include a blog for my web development and IT interests</li>
    <li>Allow site searching (everything is an article or download)</li>
</ul>
<p>
    I've written it using a strict MVC model, which means that I have libraries
    for accessing and manipulating the database; all displays are template
    driven (meaning I can create them with plain-old HTML); and I can create
    customizable applications out of various controller libraries. I've called
    this concoction <strong>Dragonfly</strong>.
</p>
<p>
    There will be more developments coming -- sitewide search comes to mind, as
    well as RSS feeds for the blog and downloads.
</p>
<p>Stay Tuned!</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;