<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('168-DHH-on-PHP');
$entry->setTitle('DHH on PHP');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1207314374);
$entry->setUpdated(1207856753);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'rails',
));

$body =<<<'EOT'
<p>
    Somebody on <a href="http://twitter.com/">Twitter</a> pointed this out, and
    I thought I'd spread the word: <a href="http://www.loudthinking.com/">DHH</a> of <a
        href="http://rubyonrails.org/">Rails fame</a> has posted a nice, short,
    and very interesting thought on <a
        href="http://www.loudthinking.com/posts/23-the-immediacy-of-php">"The
        immediacy of PHP"</a>.
</p>

<p>
    I've been delving a little into Rails lately myself, and what I find is: use
    the right tool for the job. For green-field, self-hosted projects, Rails is
    not a bad choice, and offers a very easy way to get your application up and
    running quickly. But due to the fact that PHP was built for the web, there
    are any number of tasks that are simpler and faster to accomplish using it.
    Evaluate your needs carefully, and choose the tool that best addresses them.
</p>

<p>
    It's nice to see leaders of projects like Rails having this same attitude.
    It's a breath of fresh air in the competitive market of web development
    frameworks.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;