<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('148-php-anthology-2nd-edition-is-out');
$entry->setTitle('PHP Anthology, 2nd Edition is out');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1193772787);
$entry->setUpdated(1193830680);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'books',
));

$body =<<<'EOT'
<p>
    Well, it's now official: <a href="http://www.sitepoint.com/books/phpant2/">The PHP Anthology, 2nd Edition</a>
    is finally out, and, as you'll see if you follow the link, I'm listed as an
    author on it. :-) It's a pleasant surprise to see it out -- I finished my
    chapters back in January, and had almost forgotten about it.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    For those curious, I penned Chapters 9 ('Error Handling'), 12 ('XML and Web
    Services'), and 13 ('Best Practices'). If you're a reader and have any
    feedback, I'd love to hear it.
</p>

<p>
    Now, for those wondering about my bio: I submitted a few sentences for a bio
    a few months ago, and somehow one of those got abbreviated to 'Leading PEAR
    expert' on the main book landing page. For those of you PEAR developers out
    there (Greg, Josh, Stig, and many more I cannot list), <b>you're</b> the
    real PEAR experts, and I thank you much for the many hours of coding you've
    saved me over the years. I only hope my one little contribution
    (File_Fortune) and many emails on the pear-dev list have helped any of you.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
