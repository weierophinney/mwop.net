<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('153-Sqlite-Version-Mismatch');
$entry->setTitle('Sqlite Version Mismatch');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1198170000);
$entry->setUpdated(1198170000);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I ran into an issue recently in testing a site where PDO_SQLITE was claiming
    that it could not read my PDO database files. The only recent change I'd had
    was that I'd installed a new version of PHP, and hence a new version of
    PDO_SQLITE. Searching the web (we're not supposed to say <em>googling</em>
    or <em>googled</em> anymore, remember ;-)), I found that the issue was that
    the version of sqlite compiled into my PHP install was not compatible with
    the version I used to create the databases in the first place. Never mind
    that they're only a micro version or two different.
</p>

<p>
    So, I was left with a conundrum: I needed to create files compatible with my
    PDO_SQLITE install, but my CLI sqlite tool was incompatible. And if I used
    PDO_SQLITE to create the db file, I'd lose my data, right?
</p>

<p>
    Wrong. And here's what you can do should you find yourself in the same
    situation sometime.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    The fixes hinges on three things:
</p>

<ul>
    <li>PDO_SQLITE will create the database file if it doesn't exist.</li>
    <li>Sqlite has a facility for dumping all SQL for generating and populating
    existing tables in a database file.</li>
    <li><a href="http://pear.php.net/packages/PHP_Shell">PHP_Shell</a> allows
    you to interact with PHP at the command line.</li>
</ul>

<p>
    So, here goes. First, create a SQL dump of your existing file. The sqlite
    command accepts two arguments: the database file to use, and either SQL or
    sqlite metacommands. In this case, we'll pass the command '.dump' (note the
    '.' prefix), and redirect output to a file:
</p>

<div class="example"><pre><code lang="bash">
sqlite mydata.db .dump &gt; /tmp/mydata.sql
</code></pre></div>

<p>
    Now, we need to delete the existing database file, or back it up somewhere.
    Once done, we'll fire up PHP_Shell, using the php-shell.sh command (or
    php-shell.bat for Windows users). PHP_Shell is a handy utility that provides
    an interactive PHP shell, complete with history and completion. We'll use it
    to create our sqlite database file:
</p>

<pre>
% phpshell 
PHP-Shell - Version 0.3.0, with readline() support
(c) 2006, Jan Kneschke <jan@kneschke.de>

&gt;&gt; use '?' to open the inline help 

&gt;&gt; $db = new PDO('sqlite:/path/to/mydata.db');
PDO::__set_state(array(
))
&gt;&gt; quit
</pre>

<p>
    With the database file created, we'll now load up that schema and data we
    dumped earlier. The nice part here is that the sqlite utility tends to be
    more tolerant of version differences, so we can load the data into the new
    database file using it, and PHP will be none the wiser:
</p>

<div class="example"><pre><code lang="bash">
sqlite mydata.db &lt; /tmp/mydata.sql
</code></pre></div>

<p>
    All done!
</p>
EOT;
$entry->setExtended($extended);

return $entry;