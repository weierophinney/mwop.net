<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('64-PHP-Unit-Tests-and-the-winner-is-phpt');
$entry->setTitle('PHP Unit Tests: and the winner is: phpt');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1114026551);
$entry->setUpdated(1114032861);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I've been tinkering with <a
    href="http://www.wikipedia.org/wiki/Unit_testing" target="_blank">Unit
    Testing</a> for around a year now, and have tried using <a
    href="http://pear.php.net/package/PHPUnit">PHP Unit</a> as well as <a
    href="http://www.lastcraft.com/simple_test.php">Simple Test</a>. It was
    while following the Simple Test tutorial that I finally grokked the idea of
    unit testing, and so that has been my favored class for testing.
</p>
<p>
    However, I find writing the tests tedious. In Simple Test, as in PHP Unit,
    you need to create a class that sets up the testing harness, and then you
    create a method for each test you wish to run, and so on... I found it
    incredibly time consuming. Additionally, I found the test harness often felt
    like a foreign way of testing my code; I was setting up a structure I would
    never use my code in, typically. All in all, I only test when I have extra
    time (which is rare) or when I'm really having trouble nailing down bugs
    (and the unit tests often don't help me find them).
</p>
<p>
    Recently, I've been hearing some buzz over on the <a
        href="http://pear.php.net">PEAR</a> lists and the blogs of some of its
    developers about 'phpt' tests. From what I hear, phpt tests sound very
    similar to how one tests in perl (though I've never written perl tests, I've
    at least glanced through them). However, until recently, I haven't seen any
    documentation on them, and installing PEAR packages via pear doesn't install
    tests.
</p>
<p>
    We got a copy of <a href="http://php5powerprogramming.com/">PHP5 Power
        Programming</a> a few weeks ago, and in the section on preparing a PEAR
    package was a brief section on phpt tests. The section was small, and when I
    looked at it, my immediate thought was, "it can't be <em>that</em> simple,
    can it?"
</p>
<p>
    So, I decided to try it out with <a
        href="http://cgiapp.sourceforge.net/">Cgiapp</a>. A few minutes later, I
    had some working tests for my static methods. "Hmmm," I thought, "That was
    easy. Let's try some more." 
</p>
<p>
    Turns out they're kind of addictive to geeks like me. In a matter of a few
    hours, I'd knocked out tests for over half the functionality, and
    disccovered, to my chagrine and joy, a number of bugs and bad coding
    practices... which I promptly corrected so I could get that magical 'PASS'
    from the test harness.
</p>
<p>
    In the process of writing the tests, my understanding of the tool evolved
    quite a bit, and by the end, I had the knack for it down. I'll blog later
    about some of the ways I made them easier to use for myself -- and how I
    made them more useful for debugging purposes.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;