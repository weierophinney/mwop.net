<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('226-Speaking-at-ZendCon-2009');
$entry->setTitle('Speaking at ZendCon 2009');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1255350600);
$entry->setUpdated(1255372851);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zendcon09',
));

$body =<<<'EOT'
<p>
    It's probably already a foregone conclusion, but I'm speaking once again at
    <a href="http://zendcon.com/">ZendCon</a> this year -- one week from today!
</p>

<p style="text-align: center;"><a href="http://zendcon.com/"><img src="/uploads/zendcon09_speakerbutton.jpg" /></a></p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I'm delivering three different talks this year:
</p>

<ul>
    <li>
        <a href="http://zendcon.com/tutorials#session-12095">Introduction to
            Zend Framework</a>. In this tutorial, I'll look at what ZF is, how
        to get and install it, some common utilities and patterns used
        throughout the project, and spend the bulk of the time on building,
        hands-on, a simple application.
    </li>

    <li>
        <a href="http://zendcon.com/tutorials#session-11790">Architecting Ajax
            Applications with Zend Framework</a>. Another tutorial session, this
        one will focus not so much on the client-side of an Ajax application,
        but how to architect the server-side to respond appropriately to Ajax
        (and other service) requests. We'll look at RPC services, REST, HTTP
        codes and headers, and how to weave them into a robust and simple
        backend for use with your dynamic, "Web 2.0" applications.
    </li>

    <li>
        <a href="http://zendcon.com/tracks?tid=1357#session-11787">Architecting
        Your Models</a>. This regular track session is all about the M in MVC:
        models. Many MVC frameworks and approaches have you tie the data access
        directly to your models; in this session, I'll show you approaches and
        strategies for de-coupling data access from your models, why you might
        want to do so, and some cool things you can do once you have.
    </li>
</ul>

<p>
    I'll also be involved in two panel sessions:
</p>

<ul>
    <li>
        <strong>Meet the Zend Team</strong> (Wednesday evening). This has been a
        popular session in past years; project leads from each of the product
        teams at Zend will be available in a panel to answer your questions.
        Some years have been rather... entertaining. :)
    </li>

    <li>
        <strong>Framework Shootout</strong> (Thursday, closing keynote). I'm
        especially excited about this session, in which we get myself,
        representing Zend Framework; Fabien Potencier, representing Symfony;
        David Zuilke, representing Agavi; Nate Abele, representing CakePHP; and
        Edward Finkler, representing CodeIgniter. Eli White will be moderating a
        Q&amp;A style session in which you, the audience, get to ask us about
        our respective frameworks and where we compare (or don't!) in various
        functional areas.  I did a similar panel to this at PHP Quebec this
        year, and it was a great time.
    </li>
</ul>

<p>
    There will be a ton of sessions, with people presenting on a huge variety of
    subjects, from version control to RDMS systems, from JavaScript to Flash,
    from development to production. In addition, there will be a parallel <a
        href="http://zendcon.com/uncon">UnCon</a>, being run by <a
        href="http://caseysoftware.com/blog/">Keith Casey</a>, featuring even
    more topics and interesting ideas.
</p>

<p>
    I look forward to seeing you at ZendCon this year!
</p>
EOT;
$entry->setExtended($extended);

return $entry;