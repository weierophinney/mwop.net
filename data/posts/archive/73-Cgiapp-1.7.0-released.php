<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('73-Cgiapp-1.7.0-released');
$entry->setTitle('Cgiapp 1.7.0 released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1116646323);
$entry->setUpdated(1116646769);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I'm getting ready to move in another week, and thought it was time to push a
    new release out the door... before life descends into utter chaos.
</p>
<p>
    Cgiapp 1.7.0 adds a plugin architecture (which has been present in the perl
    version since last autumn). Plugins register with the class, and, once
    registered, their methods may be called from your Cgiapp-based class as if
    they were part of it through the magic of overloading. This allows for a
    standard library of utilities to be written -- such as form validation (a
    sample class for this has been provided utilizing <a
        href="http://pear.php.net/HTML_QuickForm">HTML_QuickForm</a>),
    authentication, error logging, etc.
</p>
<p>
    Additionally, I created a 'Cgiapp5' class that inherits from and extends
    Cgiapp. Along with it is a 'CgiappErrorException' class that can handle PHP
    errors and rethrow them as exceptions. Combined, the two create some very
    elegant run mode error handling that simply isn't possible in PHP4.
</p>
<p>
    Visit the <a href="http://cgiapp.sourceforge.net/">Cgiapp website</a> for
    more information on Cgiapp; if you want to try it, <a
        href="http://prdownloads.sourceforge.net/cgiapp/Cgiapp-1.7.0.tgz?download">download
    it</a>. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;