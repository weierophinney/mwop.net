<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('145-oh,-yeah,-zendcon...');
$entry->setTitle('oh, yeah, zendcon...');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1191324449);
$entry->setUpdated(1191473167);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'conferences',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I don't know why I haven't blogged this sooner, but, yes, I'll be speaking
    once again at <a href="http://www.zendcon.com/">ZendCon:</a>
</p>
<p style="text-align:center;"><a href="http://www.zend.com.com" ><img src="http://s3.amazonaws.com/zendcon/ZendCon07_SpeakerBadge.gif" border="0"></a></p>

<p>
    I'll be presenting a number of times:
</p>

<ul>
    <li>On Monday, I join <a href="http://sebastian-bergmann.de/">Sebastian Bergmann</a> 
        and <a href="http://naberezny.com/">Mike Naberezny</a> in a full-day
        tutorial session on PHP Development Best Practices and Unit Testing.
        This expands on what Mike and I did last year, and will more heavily
        emphasize the role of testing in the development process -- arguably the
        most important best practice you can adopt.</li>
    <li>On Tuesday monrning, I'll present a Zend Framework MVC Quick Start. This
        talk is based on a <a href="http://weierophinney.net/matthew/archives/144-Zend-Framework-MVC-Webinar-posted.html">webinar</a> 
        I recently gave for Zend, and covers the various pieces of the MVC layer
        in Zend Framework.
    </li>
    <li>Tuesday evening, I'll present an <a href="http://zendcon.com/wiki/index.php?title=Uncon">Unconference</a>
        session on Ajax-enabling your Zend Framework controllers. I don't know
        yet if I'll need the whole hour, but I can probably fill it up with some
        examples of decorating your apps with AJAX.
    </li>
</ul>

<p>
    Looking forward to seeing you all there!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;