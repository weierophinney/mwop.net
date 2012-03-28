<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('128-PHP-Best-Practices');
$entry->setTitle('PHP Best Practices');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1162357265);
$entry->setUpdated(1162387861);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Yesterday, <a href="http://mikenaberezny.com/">Mike</a> and I presented our
    session "Best Practices of PHP Development" at this year's Zend Conference.
    It was a marathon three hour tutorial first thing in the morning, and we had
    an incredible turnout, with some fairly enthusiastic people in the audience.
</p>

<p>
    <a href="/uploads/php_development_best_practices.pdf" title="php_development_best_practices.pdf" target="_blank">Download the slides slides for PHP Best Practices</a>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    We ended up cutting a ton from the session the night before, as we
    discovered we actually had way too much material. Amongst the cuts were
    sections on:
</p>
<ul>
    <li>
        Comparisons of different coding standards. I'd worked up a comparison of
        eZ Components and Zend Framework standards to contrast against PEAR's.
    </li>
    <li>
        Functional testing. Mike put a lot of effort into the unit testing
        section, and I'd done an additional section on functional testing --
        testing against fixtures, such as test databases, sandbox services, etc.
    </li>
    <li>
        Repository layout. Mike actually talked about this briefly, but we'd
        intended to show some designs for subversion layouts, and how to create
        and use branches and tags.
    </li>
    <li>
        Subversion hook scripts. We mentioned their existence, and some uses,
        but we'd hoped to show how to add these to your repository, and some
        sample scripts.
    </li>
    <li>
        Mailman. How to setup archived mailing lists.
    </li>
    <li>
        Capistrano. Mike mentioned this tool in the talk, but did not have time
        to go into examples of usage.
    </li>
</ul>
<p>
    Basically, most of the topics we covered could have easily been a session in
    their own right. However, having a big block of time to cover the spectrum I
    believe helps show how to integrate the individual solutions into a set of
    cohesive development practices.
</p>
<p>
    I hope to blog about some of the areas we had to skip in coming months.
</p>
<p>
    To those attendees who came to the session yesterday, thank you for being a
    great audience!
</p>
EOT;
$entry->setExtended($extended);

return $entry;