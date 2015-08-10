<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('27-HTMLTemplate-notes');
$entry->setTitle('HTML::Template notes');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1076030300);
$entry->setUpdated(1095702263);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'perl',
  2 => 'personal',
));

$body =<<<'EOT'
<p>
    I've used HTML::Template a little, mainly in the Secret Santa project I did
    this past Christmas for my wife's family. One thing I disliked was using the
    normal syntax: &lt;TMPL_VAR NAME=IMAGE_SRC&gt; -- it made looking at it
    difficult (it wasn't always easy to tell what was an HTML tag, what was
    plain text, and what was HTML::Template stuff), and it made it impossible to
    validate my pages before they had data.
</p>
<p>
    Fortunately, there's an alternate syntax: wrap the syntax in HTML comments:
    &lt;!-- TMPL_VAR NAME=IMAGE_SRC --&gt; does the job. It uses more
    characters, true, but it gets highlighted different than HTML tags, as well,
    and that's worth a lot.
</p>
<p>
    And why do I have to say "NAME=" every time? That gets annoying. As it turns
    out, I can simply say: &lt;!-- TMPL_VAR IMAGE_SRC --&gt;, and that, too will
    get the job done.
</p>
<p>
    Finally, what about those times when I want to define a template, but have
    it broken into parts, too? Basically, I want HTML::Template to behave a
    little like SSI. No worries; there's a TMPL_INCLUDE tag that can do this:
    &lt;!-- TMPL_INCLUDE NAME="filename.tmpl" --&gt;. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;