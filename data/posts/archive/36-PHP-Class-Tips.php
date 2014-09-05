<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('36-PHP-Class-Tips');
$entry->setTitle('PHP Class Tips');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1079707797);
$entry->setUpdated(1095702973);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'personal',
  2 => 'php',
));

$body =<<<'EOT'
<p>
    We're starting to use OO in our PHP at work. I discovered when I started
    using it why I'd been having problems wrapping my head around some of the
    applications I've been programming lately: I've become accustomed in Perl to
    using an OO framework. Suddenly, programming in PHP was much easier.
</p>
<p>
    There's a few things that are different, however. It appears that you cannot
    pass objects in object attributes, and then reference them like thus:
</p>
<pre>    $object->db>query($sql)
</pre>
<p>
    PHP doesn't like that kind of syntax (at least not in versions 4.x).
    Instead, you have to pass a reference to the object in the attribute, then
    set a temporary variable to that reference whenever you wish to use it:
</p>
<pre>    $object->db =& $db;
    ...
    $db = $object->db;
    $res = $db->query($sql);
</pre>
<p>
    What if you want to inherit from another class and extend one of the
    methods? In other words, you want to use the method from the parent class,
    but you want to do some additional items with it? Simple: use
    <kbd>parent</kbd>:
</p>
<pre>    function method1()
    {
        /* do some pre-processing */

        parent::method1(); // Do the parent's version of the method

        /* do some more stuff here */
    }
</pre>
<h4>Update:</h4>
<p>
    Actually, you *can* reference objects when they are attributes of another
    object; you just have to define the references in the correct order:
</p>
<pre>    $db =& DB::connect('dsn');
    $this->db =& $db;
    ...
    $res = $this->db->query($sql);
</pre>
<p>
    I've tested the above syntax with both PEAR's DB and with Smarty, and it
    works without issue.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;