<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('46-MySQL-Miscellanae');
$entry->setTitle('MySQL Miscellanae');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1098305480);
$entry->setUpdated(1098305488);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    Inspired by a <a href="http://slashdot.org/article.pl?sid=04/10/13/2016211">Slashdot book
        review</a> of <a href="http://www.oreilly.com/catalog/hpmysql/index.html">High
        Performance MySQL</a>.
</p>
<p>
    I've often suspected that I'm not a SQL guru... little things like being
    self taught and having virtually no resources for learning it. This has been
    confirmed to a large degree at work, where our DBA has taught me many tricks
    about databases: indexing, when to use DISTINCT, how and when to do JOINs,
    and the magic of TEMPORARY TABLEs. I now feel fairly competent, though far
    from being an expert -- I certainly don't know much about how to tune a
    server for MySQL, or tuning MySQL for performance.
</p>
<p>
    Last year around this time, we needed to replace our MySQL server, and I
    got handed the job of getting the data from the old one onto the new. At the
    time, I looked into replication, and from there discovered about binary
    copies of a data store. I started using this as a way to backup data,
    instead of periodic mysqldumps.
</p>
<p>
    One thing I've often wondered since: would replication be a good way to do
    backups? It seems like it would, but I haven't investigated.
    One post on the aforementioned Slashdot article addressed this, with the
    following summary:
</p>
<ol>
     <li>Set up replication</li>
     <li>Do a locked table backup on the slave</li>
</ol>
<p>
    Concise and to the point. I only wish I had a spare server on which to
    implement it!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;