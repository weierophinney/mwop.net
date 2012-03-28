<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('160-Zend_Form-Webinar-Wednesday');
$entry->setTitle('Zend_Form Webinar Wednesday');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1204581626);
$entry->setUpdated(1204802989);
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
    Just an FYI for anyone interested: I'll be performing a webinar for this
    week's Zend Wednesday Webinar series on 
    <a href="http://framework.zend.com/manual/en/zend.form.html">Zend_Form</a>.
    You can get details on the webinar and how to register for it at 
    <a href="http://www.zend.com/en/company/news/event/webinar-zend-framework-forms">the Zend_Form webinar information page</a>.
</p>

<p>
    I'll be covering the design of Zend_Form, the basic usage and 
    various classes and plugins available, and internationalization of your
    forms. Please join me Wednesday at noon EST!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;