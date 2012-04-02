<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('105-PHP-error-reporting-for-Perl-users');
$entry->setTitle('PHP error reporting for Perl users');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1143519000);
$entry->setUpdated(1143555575);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'perl',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    On <a href="http://www.perlmonks.org">perlmonks</a> today, a user was
    needing to maintain a PHP app, and wanted to know what the PHP equivalent of
    "perl -wc script.pl" was -- specifically, they wanted to know how to run a
    PHP script from the commandline and have it display any warnings (ala perl's
    strict and warnings pragmas).
</p>
<p>
    Unfortunately, there's not as simple a way to do this in PHP as in perl.
    Basically, you need to do the following:
</p>
<ul>
    <li><strong>To display errors:</strong><ul>
        <li>In you php.ini file, set "display_errors = On", <b>or</b></li>
        <li>In your script, add the line "ini_set('display_errors', true);"</li>
    </ul></li>
    <li><strong>To show notices, warnings, errors, deprecation
        notices:</strong><ul>
        <li>In you php.ini file, set "error_reporting = E_ALL | E_STRICT", <b>or</b></li>
        <li>In your script, add the line "error_reporting(E_ALL | E_STRICT);"</li>
    </ul></li>
</ul>
<p>
    Alternatively, you can create a file with the lines:
</p>
<pre>
&lt;?php
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', true);
</pre>
<p>
    and then set the php.ini setting 'auto_prepend_file' to the path to that
    file.
</p>
<p>
    <strong>NOTE: do not do any of the above on a production system!</strong>
    PHP's error messages often reveal a lot about your applications, including
    file layout and potential vectors of attack. Turn display_errors off on
    production machines, set your error_reporting somewhat lower, and log_errors
    to a file so you can keep track of what's going on on your production
    system.
</p>
<p>
    The second part of the question was how to run a PHP script on the command
    line. This is incredibly simple: php myscript.php. No different than any
    other scripting language.
</p>
<p>
    You can get some good information by using some of the switches, though.
    <strong>'-l'</strong> turns the PHP interpreter into a linter, and can let
    you know if your code is well-formed (which doesn't necessarily preclude
    runtime or parse errors). <strong>'-f'</strong> will run the script through
    the parser, which can give you even more information. I typically bind these
    actions to keys in vim so I can check my work as I go.
</p>
<p>
    If you plan on running your code <em>solely</em> on the commandline, add a
    shebang to the first line of your script: #!/path/to/php. Then make the
    script executable, and you're good to go. This is handy for cronjobs, or
    batch processing scripts.
</p>
<p>
    All of this information is readily available in <a
        href="http://www.php.net/manual">the PHP manual</a>, and the commandline
    options are always available by passing the --help switch to the PHP
    executable. So, start testing your scripts already!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;