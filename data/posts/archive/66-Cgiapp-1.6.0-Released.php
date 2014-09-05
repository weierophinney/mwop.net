<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('66-Cgiapp-1.6.0-Released');
$entry->setTitle('Cgiapp 1.6.0 Released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1114228231);
$entry->setUpdated(1114461222);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Cgiapp 1.6.0, "Wart Removal", has been released!
</p>
<p>
    This release does not add any new methods, but adds quite a lot in terms of
    functionality:
</p>
<ul>
    <li><b>phpt tests.</b> I finished writing a suite of unit tests using the
    phpt framework popularized by the PHP-QA project and PEAR. This process
    helped me find some obscure bugs in the class, as well as some... well,
    downright ugly code, and to fix these areas. (To be honest, most of the
    'ugly' code was a result of being too literal when porting from perl and not
    using more standard PHP functionality.) Among the bugs fixed:
        <ul>
            <li>s_delete() now works properly.</li>
            <li>param() and s_param() now behave gracefully when given bad data
            (as do a number of other methods)</li>
            <li>_send_headers() and the header_*() suite now function as
            documented.</li>
            <li>All methods now react gracefully to bad input.</li>
        </ul>
    </li>
    <li><b>Error handling.</b> carp() and croak() no longer echo directly to the
    output stream (and, in the case of croak(), die); they use trigger_error().
    This will allow developers to use carp() and croak() as part of their
    regular arsenal of PHP errors -- including allowing PHP error handling.
    Additionally, most croak() calls in the class were changed to carp() as they
    were not truly fatal errors.</li>
    <li><b>PEAR packaging.</b> Cgiapp can now be installed using PEAR's
    installer. Simply download the package and type 'pear install
    Cgiapp-1.6.0.tgz' to get Cgiapp installed sitewide on your system!</li>
</ul>
<p>
    As usual, Cgiapp is available at <a
        href="http://cgiapp.sourceforge.net/">the Cgiapp website</a>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;