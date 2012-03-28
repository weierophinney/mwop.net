<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('178-Zend-FrameworkDojo-Integration-QA-Slides');
$entry->setTitle('Zend Framework/Dojo Integration QA Slides');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1211906163);
$entry->setUpdated(1212096369);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'webinar',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    We had our Zend Framework/Dojo integration QA today. Aside from some
    connectivity issues at the beginning of the presentation, things went
    smoothly, and there were some good questions.
</p>

<p>
    A number of people reported missing the slides or that the slides were not
    advancing. I'm posting them here; they will also be available on <a
        href="http://www.zend.com/webinars">the Zend.com webinars page</a> later
    this week.
</p>

<p>
    <a href="http://weierophinney.net/uploads/2008-05-27-ZendFramework_Dojo.ppt">ZendFramework_Dojo.ppt</a>
</p>

<p>
    <b>Update:</b> For those who want to view online, you can now do so at <a href="http://www.slideshare.net/weierophinney/zend-framework-and-dojo-integration-faq/">SlideShare</a>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;