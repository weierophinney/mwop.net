<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('32-Cgiapp-A-PHP-Class');
$entry->setTitle('Cgiapp: A PHP Class');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1080701851);
$entry->setUpdated(1095702688);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'perl',
  2 => 'personal',
  3 => 'php',
));

$body =<<<'EOT'
<p>
    After working on some OO classes yesterday for an application backend I'm
    developing for work, I decided I needed to create a <tt>BREAD</tt> class to
    make this simpler. You know,
    <b>B</b>rowse-<b>R</b>ead-<b>E</b>dit-<b>A</b>dd-<b>D</b>elete.
</p>
<p>
    At first, I figured I'd build off of what I'd done yesterday. But then I got
    to thinking (ah, thinking, my curse). I ran into the <tt>BREAD</tt> concept
    originally when investigating <tt>CGI::Application</tt>; a number of
    individuals had developed <tt>CGI::Apps</tt> that provided this
    functionality. I'd discarded them usually because they provided more
    functionality than I needed or because they introduced more complexity than
    I was willing to tackle right then.
</p>
<p>
    But once my thoughts had gone to <tt>BREAD</tt> and <tt>CGI::App</tt>, I
    started thinking how nice it would be to have <tt>CGI::Application</tt> for
    PHP. And then I thought, why not? What prevents me from porting it? I have
    the source...
</p>
<p>
    So, today I stayed home with Maeve, who, on the tail end of an illness,
    evidently ran herself down when at daycare yesterday, and stayed home
    sleeping most of the day. So, while she was resting, I sat down with a
    printout of the non-POD code of <tt>CGI::App</tt> and hammered out what I
    needed to do. Then, when she fell asleep for a nap, I typed it all out and
    started testing. And, I'm proud to say, it works. For an example, visit <a href="http://dev.weierophinney.net/cgiapp/test.php">my development
        site</a> to see a very simple, templated application in action.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;