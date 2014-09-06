<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('112-Introducing-Phly_Struct-and-Phly_Config');
$entry->setTitle('Introducing Phly_Struct and Phly_Config');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1148072400);
$entry->setUpdated(1148093696);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I often find myself needing a configuration module of some sort -- for
    storing application parameters, bootstrapping, template variables, what have
    you. I typically will either:
</p>
<ol>
    <li>Create a PHP file that creates and returns an array, and suck that in
    via <a href="http://www.php.net/include">include</a>, or</li>
    <li>Create an INI file and suck it in via 
    <a href="http://www.php.net/parse_ini_file">parse_ini_file</a>, or</li>
    <li>Create an XML file and suck it in via 
    <a href="http://www.php.net/simplexml">SimpleXML</a>.</li>
</ol>
<p>
    The first method gives great flexibility of structure and types, but isn't
    portable to other languages (well, not easily; you could turn it into JSON,
    or serialize it, etc).  The second method (INI files) is handy because the
    syntax is so concise, and can translate to other projects in other languages
    easily if necessary; however, you can only easily go two levels deep (using
    [sections] in the file). The third method is very portable, and allows
    nested structures -- but doesn't allow usage of many specific PHP types.
</p>
<p>
    I find, however, that each has their place. The problem, however, is: once I
    bring them into my project, how can I access them? Better yet, would there
    be a way to bring in configurations of many types and still access them all
    in the same way?
</p>
<p>
    Not happy with solutions out there, I did the only logical thing: I
    reinvented the wheel, and added some new tread of my own. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    The solution started out as 
    <a href="http://weierophinney.net/phly/index.php?package=Phly_Config">Phly_Config</a>,
    which was going to be a generic storage container with several adapters for
    loading configurations. However, in talking with 
    <a href="http://paul-m-jones.com/blog/">Paul</a>, he noticed two tools
    emerging. To quote him:
</p>
<ol>
    <li>A general-purpose array/object/struct implementation.</li>
    <li>Config adapters to push data into the struct.</li>
</ol>
<p>
    I got to thinking about it, and decided he was right -- and so 
    <a href="http://weierophinney.net/phly/index.php?package=Phly_Struct">Phly_Struct</a>
    was born.
</p>
<p>
    Now that I've hyped it up, just what do the tools offer? I think examples
    sum it up best. First, Phly_Struct:
</p>
<div class="example"><pre><code lang="php">
$array = array(
    'key1 =&gt; 'value1',
    'key2 =&gt; array(
        'subkey1' =&gt; array(
            'subsubkey1' =&gt; 'subsubvalue1',
            'subsubkey2' =&gt; 'subsubvalue2'
        ),
        'subkey2' =&gt; 'subvalue2'
    ),
    'key3' =&gt; 'value3'
);
$struct = new Phly_Struct($array);

echo 'The value of the second subkey is ' . $struct-&gt;key2-&gt;subkey2;
$struct-&gt;key3 = 'Some new value!';
$struct-&gt;key2-&gt;subkey1-&gt;subsubkey1 = 'I am a third level value';

// Let's loop over values...
foreach ($struct-&gt;key2 as $key =&gt; $value) {
    if (is_scalar($value)) {
        echo \&quot;Key $key: $value\n\&quot;;
    }
}

// Let's grab a sub array to pass elsewhere
$subarray = $struct-&gt;key2-&gt;subkey1-&gt;toArray();
</code></pre></div>
<p>
    As you can see, Phly_Struct provides object oriented access to values. You
    can retrieve and set them just as you would object properties. Additionally,
    since it implements the <a href="http://www.php.net/spl">SPL</a> Iterator,
    you can loop over a Phly_Struct just like you would an array.
</p>
<p>
    Now let's turn to Phly_Config:
</p>
<div class="example"><pre><code lang="php">
$array = array(
    'key1 =&gt; 'value1',
    'key2 =&gt; array(
        'subkey1' =&gt; array(
            'subsubkey1' =&gt; 'subsubvalue1',
            'subsubkey2' =&gt; 'subsubvalue2'
        ),
        'subkey2' =&gt; 'subvalue2'
    ),
    'key3' =&gt; 'value3'
);
// Load an in-memory array into the config
Phly_Config::load($array);

// Grab configuration; Phly_Config is a singleton, so this can be done at any
// time, and any configurations loaded will be available
$config = Phly_Config::getInstance();

// Load an INI file into the config, under the namespace 'ini'
Phly_Config::load('/path/to/some/config.ini', 'ini');

// Load an XML file into the config, under the namespace 'xml'
Phly_Config::load('/path/to/some/config.xml', 'xml');

// Load a PHP file returning an array into the config, under the namespace 'php'
Phly_Config::load('/path/to/some/config.php', 'php');

// By default, values are retrieved from the 'default' namespace, which is used
// if no namespace was specified when loading a config:
echo 'I am the third value: ' . $config-&gt;key3;

// Now grab the db host from the 'ini' namespace
$host = $config-&gt;ini-&gt;db-&gt;host;

// Set the db host in the xml config namespace
$config-&gt;xml-&gt;db-&gt;host = $host;

// Let's get back the whole db array from the xml config namespace
$db_data = $config-&gt;xml-&gt;db-&gt;toArray();
</code></pre></div>
<p>
    Since Phly_Config simply loads configuration structures into Phly_Structs,
    you get all the syntactic yumminess of that class for free. Additionally,
    since Phly_Config is a singleton class, you can load configurations at
    different points in the code, and have access to those values anywhere else.
</p>
<p>
    Now, you <em>can</em> set values with Phly_Config. However, they will only
    affect the current instance; nothing gets written back to the configuration
    files at this point. Typically, configuration is a one way street; you want
    to load configuration data and access it from your program, but not change
    it. I may in the future add implementations for writing configurations, or
    at least spitting out the formatted output. In the meantime, you can always
    serialize the object either with PHP or as JSON.
</p>
<p>
    Comments, bugfixes, and other feedback always welcome!
</p>
EOT;
$entry->setExtended($extended);

return $entry;