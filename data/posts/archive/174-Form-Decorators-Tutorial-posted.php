<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('174-Form-Decorators-Tutorial-posted');
$entry->setTitle('Form Decorators Tutorial posted');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1209997500);
$entry->setUpdated(1209997500);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    As a continuing part of my MVC series, I've posted a new article on 
    <a href="http://devzone.zend.com/article/3450-Decorators-with-Zend_Form">Form Decorators</a>
    up on the DevZone.
</p>

<p>
    I'm hoping this will be the definitive guide to using form decorators. I
    cover the design decisions behind them, basics of operation, how to
    customize output by mixing and matching standard decorators, and how to
    create your own custom decorators. Among the examples are how to create a
    table-based layout for your forms (instead of the dynamic list layout used
    by default), and how to use a View Script as your form decorator in order to
    have full control over your form layout.
</p>

<p>
    So, if you've been playing with Zend_Form and having trouble wrapping your
    head around decorators, <a href="http://devzone.zend.com/article/3450-Decorators-with-Zend_Form">give it a read</a>!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;