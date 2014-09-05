<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('130-Getting-XDebug-working-with-PHPUnit-for-code-coverage-reports');
$entry->setTitle('Getting XDebug working with PHPUnit for code coverage reports');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1167332340);
$entry->setUpdated(1167422426);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I've been playing with <a href="http://phpunit.de/">PHPUnit</a> a lot of
    late, particularly with 
    <a href="http://framework.zend.com/">framework development</a>.
    One thing that's always hard to determine is how well your code is exercised
    -- basically, how much of the code is tested in the unit tests?
</p>
<p>
    In PHPUnit 3, you can now generate code coverage reports using 
    <a href="http://xdebug.org">XDebug</a>, and the usage is very simple:
</p>
<pre>
matthew@localhost:~/dev/zend/framework-svn/tests$ phpunit --report ~/tmp/report AllTests
</pre>
<p>
    The above command creates a coverage report directory 'report' under my tmp
    directory. You can then browse through the reports in a web browser and
    visually see which lines of code were executed during tests, and which were
    not, as well as a synopsis showing the percentage of coverage for any given
    file or directory -- useful stuff indeed!
</p>
<p>
    So, what's the problem? Getting XDebug running.
</p>
<p>
    The executive summary:
</p>
<ul>
    <li>Enable the extension using <kbd>zend_extension =
        /full/path/to/xdebug.so</kbd>, not as <kbd>extension =
        xdebug.so</kbd>, in your php.ini</li>
    <li>Use the setting <kbd>xdebug.default_enable = Off</kbd> in your
    php.ini.</li>
    <li>If compiling using pecl or pear, make sure it compiles against the
    correct PHP; if not, hand compile it using:
    <pre>
$ /path/to/phpize
$ ./configure --with-php-config=/path/to/php-config
$ make
$ make install
</pre>
    </li>
</ul>
<p>
    For the detailed narrative, read on.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    First off, I tried installing XDebug using pecl and pear. Even though my
    'pear config-show' showed my correct PHP install and extension directory,
    for some reason it found the PHP 4.4.1 installation I have elsewhere in the
    filesystem, and it compiled against that. So, I followed the directions for
    compiling by hand, and all was mostly well. I discovered, however, that you
    need to specify the <kbd>--with-php-config=/path/to/php-config</kbd> switch
    to ensure that it uses the appropriate php-config (particularly if you have
    multiple PHP installs on your system).
</p>
<p>
    Next up was getting it to work with PHP. I edited my php.ini file, and did a
    standard <kbd>extension=xdebug.so</kbd>. What was odd is that I then showed
    xdebug as present (using <kbd>php -m</kbd>), but not as a Zend extension. I
    tried <kbd>zend_extension=xdebug.so</kbd>, but then nothing showed. Then, in
    the end, I followed the instructions, and used
    <kbd>zend_extension=/full/path/to/xdebug.so</kbd>, and it was available.
</p>
<p>
    Okay, let's test it out... I started running tests and... segmentation
    fault. Disabling the extension brought everything back to normal... only
    when enabled did the segmentation fault occur.  I decided to look at the
    xdebug php.ini settings to see what I could find.
</p>
<p>
    After some trial and error, I discovered that setting
    <kbd>xdebug.default_enable = Off</kbd> fixed the issue, and I was able to
    start generating some wonderful coverage reports.
</p>
<p>
    Now, to write more tests...
</p>
EOT;
$entry->setExtended($extended);

return $entry;