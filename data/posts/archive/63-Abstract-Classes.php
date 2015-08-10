<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('63-Abstract-Classes');
$entry->setTitle('Abstract Classes');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1113719055);
$entry->setUpdated(1114026511);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I just had to add a note over on PHP.net regarding abstract classes and
    methods: 
    <a href="http://php.net/language.oop5.abstract">Object Abstraction</a>.
</p>
<p>
    I'm working on Cgiapp2, which is a PHP5-only implementation of Cgiapp that
    is built to utilize PHP5's new object model as well as exceptions. One thing
    I decided to do, initially, was to make it an abstract class, and to mark
    the overridable methods as abstract as well.
</p>
<p>
    In testing, I started getting some strange errors. Basically, it was saying
    in my class extension that an abstract method existed, and thus the class
    should be marked as abstract, and, finally, that this means it wouldn't run.
</p>
<p>
    What was so odd is that the method didn't exist in the extension at all.
</p>
<p>
    So, I overrode the method in the extension... and voila! Everything worked
    fine.
</p>
<p>
    The lesson to take away from this is quite simple: if the method does not
    need to be present in the overriding class, don't mark it as abstract. Only
    mark a method as abstract if:
</p>
<ol>
    <li>The method is required in the class implementation, and</li>
    <li>The extending class should be responsible for implementing said
    method</li>
</ol>
<p>
    Now I need to update my source tree.... :-(
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;