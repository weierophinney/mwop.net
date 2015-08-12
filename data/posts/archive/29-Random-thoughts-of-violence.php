<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('29-Random-thoughts-of-violence');
$entry->setTitle('Random thoughts of violence');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1076383556);
$entry->setUpdated(1095702404);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'aikido',
));

$body =<<<'EOT'
<p>
    I began the day with sudden images and body remembrances of an escrima or
    arnis drill Morgan used to teach during weapons class years ago -- it
    utilizes a short stick or wakazashi in one hand, the other hand free, and
    consists of five steps on each side; when you finish one side, you do the
    other, because the drill is done with a partner.
</p>
<p>
    I haven't done the drill for years, but I remembered all the nuances, all
    the little tips and secrets Morgan showed me over the year or two he
    continued teaching it. And I wanted desperately to do it with someone right
    that moment as I was getting out of bed so that I wouldn't lose it. But, of
    course, I had no such opportunity. The movement is still tracing its way
    through my body.
</p>
<p>
    And this evening, we watched Fight Club. I still remember watching it in the
    theater, and how it affected me then -- and it affects me in many of the
    same ways now. There's some cultural references I 'get' more now --
    references to Ikea, and now I understand groups and guided meditation and
    therapy better. And there's new references, too -- the image of the
    buildings falling is much different now that the WTC buildings have been
    viewed collapsing.
</p>
<p>
    But the message, the message is still the same, still present. Do things own
    us, or do we own them? What do I most want to do before I die, and am I
    doing it? These are big questions for a film to raise, and I'm still
    surprised that Fight Club remains such a huge hit and success because of
    them. And they're not necessarily buried in the film -- though I can see how
    many people might simply glorify the violence in the film, and pass over the
    message. I find the violence is a part of the message -- can you teach
    yourself to live with pain, that pain is transient and ceases? can you learn
    to stop living in fear?
</p>
<p>
    So my day was marked by violence, beginning and end. The middle was all
    consumer fluff. And hedonism. But hey, that's okay, too.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;