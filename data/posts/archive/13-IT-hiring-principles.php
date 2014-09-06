<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('13-IT-hiring-principles');
$entry->setTitle('IT hiring principles');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074865930);
$entry->setUpdated(1095131816);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
  2 => 'php',
));

$body =<<<'EOT'
<p>
    I was just reading an <a href="http://linuxjournal.com/article.php?sid=7372&amp;mode=thread&amp;order=0">article
        about the Dean campaign's IT infrastructure</a>, and there's an
    interesting quote from their IT manager, Harish Rao:
</p>
<blockquote>
    "I believe in three principles", he said. "First I always make sure I hire
    people I can trust 100%. Second, I always try to hire people who are smarter
    than I am. Third, I give them the independence to do as they see fit as long
    as they communicate about it to their other team members. We've had a lot of
    growing pains, a lot of issues; but we've been able to deal with them
    because we have a high level of trust, skill and communication."
</blockquote>
<p>
    I know for myself that when I (1) don't feel trusted, and/or (2) am not
    given independence to do what I see as necessary to do my job, I don't
    communicate with my superiors about my actions, and I also get lazy about my
    job because I don't feel my work is valued.
</p>
<p>
    Fortunately, I feel that in my current work situation, my employers followed
    the same principles as Rao, and I've felt more productive and appreciated
    than I've felt in any previous job.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;