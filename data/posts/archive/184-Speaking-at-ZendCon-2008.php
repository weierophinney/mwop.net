<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('184-Speaking-at-ZendCon-2008');
$entry->setTitle('Speaking at ZendCon 2008');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1219841385);
$entry->setUpdated(1219860414);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
  3 => 'zendcon08',
));

$body =<<<'EOT'
<p style="text-align: center"><a href=http://zendcon.com><img src="http://zendcon.s3.amazonaws.com/ZendCon08_speaker_badge.gif" border="0"/></a></p>
<p>
    I'll be speaking at <a href="http://www.zendcon.com/">ZendCon</a> again this
    year, and have a four-course meal of sessions to deliver:
</p>

<ul>
    <li>
        <p>
            <b>Best Practices of PHP Development</b>: <a href="http://mikenaberezny.com/">Mike Naberezny</a> 
            and I are teaming up for the fourth year running to deliver a
            tutorial session. While the session topic stays the same, he and I
            have each been developing a number of new practices over the past
            year that we look forward to presenting, including new work with
            PHPUnit for functional testing of your applications.
        </p>
    </li>

    <li>
        <p>
            <b>Getting Started with Zend Framework</b>: This will build off our
            <a href="http://framework.zend.com/docs/quickstart">Quick Start</a>,
            providing background on ZF as well as the basic tools and
            information needed to get your first ZF application up and running.
            I also hope to demonstrate how the current preview of Zend_Tool can
            simplify this dramatically.
        </p>
    </li>

    <li>
        <p>
            <b>Zend_Layout and Zend_Form</b>: This session will show off
            features of Zend_Layout and Zend_Form. (Note: the subject matter may
            change.)
        </p>
    </li>

    <li>
        <p>
            <b>UnCon: Rich UIs and Easy XHR with Dojo and Zend Framework</b>:
            For those unable to attend my webinar next week, or who simply want
            to see this in person, I'll be presenting my Dojo and Zend Framework
            talk during an UnCon session. I have developed a simple app to
            showcase various features of the Dojo/ZF integration, and to show
            how easy it is to quickly develop and then scale applications that
            have great, dynamic interfaces.
        </p>
    </li>
</ul>

<p>
    Looking forward to seeing you in California in September!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;