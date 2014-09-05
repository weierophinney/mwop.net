<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('16-robots.txt');
$entry->setTitle('robots.txt');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074896645);
$entry->setUpdated(1095700963);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    One thing I've wondered about is the syntax of the robots.txt file, where
    it's placed, and how it's used. I've known that it is used to block spiders
    from accessing your site, but that's about it. I've had to look into it
    recently because we're offering free memberships at work, and we don't want
    them indexed by search engines. I've also wondered how we can exclude
    certain areas, such as where we collate our site statistics, from these
    engines.
</p>
<p>
    As it turns out, it's really dead simple. Simply create a
    <tt>robots.txt</tt> file in your htmlroot, and the syntax is as follows:
</p>
<pre>User-agent: *
Disallow: /path/
Disallow: /path/to/file
</pre>
<p>
    The <tt>User-agent</tt> can specify specific agents or the wildcard; there
    are so many spiders out there, it's probably safest to simply disallow all
    of them. The <tt>Disallow</tt> line should have only one path or name, but
    you can have multiple <tt>Disallow</tt> lines, so you can exclude any number
    of paths or files.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;