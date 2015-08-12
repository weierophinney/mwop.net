<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('97-Review-phparchitects-Guide-to-PHP-Security');
$entry->setTitle('Review: php|architect\'s Guide to PHP Security');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1129503180);
$entry->setUpdated(1129503302);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I flew in to San Jose today to visit Zend, and later attend the 
    <a href="http://zend.kbconferences.com">Zend/PHP Conference and Expo</a>
    (two days left... <a href="http://guest.cvent.com/i.aspx?5S,M3,a72c7fa1-43cb-4e9f-8b8e-a8b0ed99b197">register now</a> 
    if you haven't, and have the time to attend; the conference sessions promise
    to be <em>very</em> interesting).
</p>
<p>
    During the flight, I had plenty of time to go through 
    <a href="http://ilia.ws">Ilia's</a> <em>Guide to PHP Security</em>, which
    I'd ordered several weeks ago, but hadn't had time to read since.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    The thing about PHP security, and web security in general, is that there are
    some very simple rules that have been around for a long time, most important
    of which is: never trust your users. 
</p>
<p>
    Perl developed a special mode, taint mode, to deal with user input -- if
    input hasn't been filtered and verified, it's considered tainted (and the
    standard method for doing so is passing the data through a regexp).
    Additionally, it's considered good practice in perl to 'use strict' and 'use
    warnings', as these two pragmas will let the developer know when they're
    succumbing to bad habits.
</p>
<p>
    Ilia takes the lens of security and applies it to the PHP language.  His
    book is a pragmatic look at how to safely handle user input to prevent such
    things as XSS attacks, SQL injection, and more. Most importantly, he
    explains what the various dangers are, gives some examples of how to create
    the attacks -- and then some methods for defending your script from them. 
</p>
<p>
    Covered are XSS attacks, SQL injection, code injection (via include files),
    command injection, sessions, and session hijacking. He also has some tips on
    being proactive -- building sandboxes and tar pits in which to monitor
    hacker activity so you can see what you're up against.
</p>
<p>
    I found that much of the theory that Ilia writes about is not new. However,
    his examples often contained some nuggets of experience I'd never
    considered. For instance, I have often used ImageMagick, but never
    considered what would happen if I tried to convert an animated GIF to
    another format (it creates several files, one for each frame) -- and how
    that might affect my script (the expected filename will not be present). Nor
    had I considered that character input may actually come in encoded -- which
    would often be either invalid for the filters I create, or completely bypass
    them.
</p>
<p>
    On one particular point, I feel I must congratulate Ilia: he details the
    complexity of creating secure applications, and also goes to some lengths to
    show how just about any countermeasure can still be foiled by a determined
    hacker. 100% secure applications that utilize user input are virtually
    impossible -- but that doesn't mean we shouldn't strive for that goal.
</p>
<p>
    The book is well written, in a conversational, almost conspiratorial, voice.
    (Having had the pleasure of attending a session of Ilia's at php|Tropics, I
    can say that his written voice is very similar to his presentation voice,
    and very easy to follow.) 
</p>
<p>
    However, the book suffers from what appears to me to be quick editing --
    there are quite a number of typographical errors throughout (I'd say, on
    average, one every three pages), and a few areas where large sentences or
    paragraphs should have been rewritten prior to publication.  Layout also had
    a few issues; on page 87, for instance, the page number was injected into
    the text of the third paragraph, instead of placed at the page bottom, and
    many examples started with a single line on the end of one page and
    continued on the following page (a page break prior would have made these
    easier to read).
</p>
<p>
    All told, however, these editing and layout issues did not subtract from the
    message. Ilia's book is a strong wake-up call to any php developer worth his
    or her salt, and should be a part of any PHP developer's library.
</p>
EOT;
$entry->setExtended($extended);

return $entry;