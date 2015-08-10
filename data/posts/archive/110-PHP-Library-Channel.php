<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('110-PHP-Library-Channel');
$entry->setTitle('PHP Library Channel');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1147752060);
$entry->setUpdated(1147783593);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I've been working on <a href="http://cgiapp.sourceforge.net/">Cgiapp</a> in
    the past few months, in particular to introduce one possibility for a Front
    Controller class. To test out ideas, I've decided to port areas of my
    personal site to Cgiapp2 using the Front Controller. Being the 
    programmer I am, I quickly ran into some areas where I needed some reusable
    code -- principally for authentication and input handling.
</p>
<p>
    I've been exposed to a ton of good code via <a href="http://pear.php.net/">PEAR</a>, 
    <a href="http://www.solarphp.com/">Solar</a>, 
    <a href="http://ez.no/products/ez_components">eZ components</a>, and 
    <a href="http://framework.zend.com/">Zend Framework</a>. However, I have
    several criteria I need met:
</p>
<ul>
    <li>I want PHP5 code. I'm coding in PHP5, I should be able to use PHP5
    libraries, not PHP4 libraries that work in PHP5 but don't take advantage of
    any of its features.</li>
    <li>I prefer few dependencies, particularly lock-in with existing
    frameworks. If I want to swap out a storage container from one library and
    use one from another, I should be free to do so without having to write
    wrappers so they'll fit with the framework I've chosen. Flexibility is
    key.</li>
    <li>Stable API. I don't want to have to change my code every few weeks or
    months until the code is stable.</li>
    <li>I should be able to understand the internals quickly.</li>
</ul>
<p>
    So what did I choose? To reinvent the wheel, of course!
</p>
<p>
    To that end, I've opened a new PEAR channel that I'm calling 
    <a href="http://weierophinney.net/phly/">PHLY, the PHp LibrarY</a>, named
    after my blog. The name implies soaring, freedom, and perhaps a little
    silliness.
</p>
<p>
    It is designed with the following intentions:
</p>
<ul>
    <li>Loosely coupled; dependencies should be few, and no base class should be necessary.</li>
    <li>Extendible; all classes should be easily extendible. This may be via observers, interfaces, adapters, etc.. The base class should solve 80% of usage, and allow extensions to the class to fill in the remainder.</li>
    <li>Designed for PHP5 and up; all classes should make use of PHP5's features.</li>
    <li>Documented; all classes should minimally have excellent API-level documentation, with use cases in the class docblock.</li>
    <li>Tested; all classes should have unit tests accompanying them.</li>
    <li>Open source and commercial friendly; all classes should use a commercial-friendly open source license. The BSD license is one such example.</li>
</ul>
<p>
    Please feel free to use this code however you will. Comments, feedback, and
    submissions are always welcome.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;