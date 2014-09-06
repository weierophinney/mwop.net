<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('173-Zend-Framework-QA-Session');
$entry->setTitle('Zend Framework Q&amp;A Session');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1209408600);
$entry->setUpdated(1209408600);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'webinar',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    With 1.0 and 1.5 of <a href="http://framework.zend.com/">Zend Framework</a>
    now released, there are a lot of questions flying around -- what will we do
    next, what components to expect, what are some of the best practices, etc.
    So, we're going to have an open 
    <a href="http://devzone.zend.com/article/3448-Zend-Framework">Question and
        Answer Session</a> webinar, with all of us on the internal team.
</p>

<p>
    If you have a question you want answered, please be kind enough to <a href="http://framework.zend.com/wiki/pages/viewpage.action?pageId=43922">submit
    your question in advance</a>, so we have time to actually think about it
    (though you can always broadside us during the webinar).
</p>

<p>
    <a href="http://www.zend.com/en/company/news/event/webinar-zend-framework-the-big-q-a">Sign up</a> 
    in advance so you don't miss out!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;