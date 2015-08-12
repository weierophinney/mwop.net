<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('57-New-Cgiapp-Site');
$entry->setTitle('New Cgiapp Site');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1108841888);
$entry->setUpdated(1111809078);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
  1 => 'programming',
  2 => 'personal',
));

$body =<<<'EOT'
<p>
    I've been extremely busy at work, and will continue to be through the end of
    March. I realized this past week that I'd set a goal of having a <a
        href="http://sourceforge.net" target="_blank">SourceForge</a> website up
    and running for Cgiapp by the end of January -- and it's now mid-February.
    Originally, I was going to backport some of my libraries from PHP5 to PHP4
    so I could do so... and I think that was beginning to daunt me a little. 
</p>
<p>
    Fortunately, I ran across a quick-and-dirty content management solution
    yesterday called <a href="http://gunther.sourceforge.net/"
        target="_blank">Gunther</a>. It does templating in Smarty, and uses a
    wiki-esque syntax for markup -- though page editing is limited to admin
    users only (something I was looking for). I decided to try it out, and
    within an hour or so had a working site ready to upload.
</p>
<p>
    Cgiapp's new site can be found at <a
        href="http://cgiapp.sourceforge.net/">cgiapp.sourceforge.net</a>.
</p>

<h4>UPDATE</h4>
<p>
    Shortly after I wrote this original post, I figured out what the strength of
    Gunther was -- and why I no longer needed it. Gunther was basically taking
    content entered from a form and then inserting that content (after some
    processing for wiki-like syntax) into a Smarty template. Which meant that I
    could do the same thing with Cgiapp and <a
        href="http://pear.php.net/text_wiki">Text_Wiki</a>. Within an hour, I
    wrote an application module in Cgiapp that did just that, and am proud to
    say that the Cgiapp website is 100% Cgiapp.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;