<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('155-Zend_Form-is-ready-for-testing');
$entry->setTitle('Zend_Form is ready for testing');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1200601581);
$entry->setUpdated(1200713744);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
));

$body =<<<'EOT'
<p>I've spent the last couple months gathering requirements for the Zend_Form component, building a prototype, creating a composite proposal based on existing proposals and my research, gathering community feedback, and, finally coding the implementation. It's a testament to the value of Test Driven Development that I now have 302 unit tests passing covering the codebase...  all in just over a week's time.</p>



<p>So, if you're interested in Zend_Form, now is the time to start testing it.  You can grab it from subversion, where you'll find it in the incubator. You can find <a href="http://framework.zend.com/wiki/display/ZFDEV/Zend_Form+Notes">preliminary documentation</a> on the Framework wiki.</p><!-- technorati tags begin --><p style="font-size:10px;text-align:right;">Tags: <a href="http://technorati.com/tag/zendframework" rel="tag">zendframework</a>, <a href="http://technorati.com/tag/%20php" rel="tag"> php</a>, <a href="http://technorati.com/tag/%20zend_form" rel="tag"> zend_form</a></p><!-- technorati tags end -->
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;