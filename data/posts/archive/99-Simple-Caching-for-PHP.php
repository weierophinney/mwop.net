<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('99-Simple-Caching-for-PHP');
$entry->setTitle('Simple Caching for PHP');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1131478349);
$entry->setUpdated(1131548482);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I ran across an article on <a href="http://www.phpit.net/article/build-caching-system-php/trackback/">"How to build a simple caching system, with PHP"</a> 
    on PHPit today. Overall, it's a fairly decent article, and uses some good
    principles (using the output buffer to capture content, using a callback to
    grab the captured content). There are a few minor improvements I'd make, however.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    There are some definite areas I'd change. First off, die() doesn't make
    sense to me as a way to abort execution if content is found. exit(0) makes
    more sense -- it indicates that execution was successful.
</p>
<p>
    Also, it's often useful to have the ability to clear the cache for a given
    page. Adding a check for a $_GET or $SERVER['PATH_INFO'] element could
    accomodate that.
</p>
<p>
    The article says to include the file with the caching functions on every
    page. I have a couple issues with that: 
</p>
<ol>
     <li>Too easy to forget to include it.</li>
     <li>Namespacing -- using function in the global area could conflict with
     other user defined functions.</li>
</ol>
<p>
    To solve (1), use an auto_prepend_file directive. You can do this in a
    .htaccess file ('php_value "auto_prepend_file" "/path/to/cache.php"), in the
    httpd.conf, or directly in your php.ini; in either case, it only needs to be
    defined once, and you never have to worry about it in your scripts. If there
    are scripts you never want to cache, you can override the value in
    individual .htaccess directives, or setup a hash-lookup in the caching
    routines to skip such scripts.
</p>
<p>
    To solve (2), wrap the functions into a class. You could still call items
    statically (Cache::get_url(), etc.). This would also allow you to define the
    hash lookup as noted above -- simply place it in a static property.
</p>
<p>
    Finally, this has all been done before. <a href="http://pear.php.net/Cache_Lite">PEAR::Cache_Lite</a> 
    offers all of this functionality (minus the routines to create unique
    identifiers per page), and a little more -- efficiently, even. The only
    difference I've seen in practice is that when using PEAR::Cache_Lite, I had
    to do an auto_append_file as well to stop the output buffering.
</p>
<p>
    Overall, a nice article and introduction to page caching.
</p>
EOT;
$entry->setExtended($extended);

return $entry;