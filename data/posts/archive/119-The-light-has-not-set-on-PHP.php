<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('119-The-light-has-not-set-on-PHP');
$entry->setTitle('The light has not set on PHP');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1149615300);
$entry->setUpdated(1149684119);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'programming',
));

$body =<<<'EOT'
<p>
    I ran across a blog post entitled "<a href="http://blog.develix.com/frog/user/cliff/article/2006-06-04/9">Why the Light Has Gone Out on LAMP</a>" 
    earlier today, and felt compelled to respond.
</p>
<p>
    First, a rant: somehow, this article got posted on <a href="http://slashdot.org/">Slashdot</a>. 
    I've never heard of the guy who posted it, and some quick googling shows
    that he's a pythoner. He's simply fueling the language wars, and the
    slashdot post opened up a huge debate that need not have occurred. I think
    it was irresponsible of the Slashdot editors to post it.
</p>
<p>
    In the post, the author makes an analogy of using PHP + MySQL as the
    equivalent of using BASIC, and then uses a quote that claims BASIC "has
    become the leading cause of brain-damage in proto-hackers." 
</p>
<p>
    I'm sorry, but using a language doesn't cause brain damage. And there are
    many levels to programming. And using Python, Ruby, C, C++, Java, etc., does
    not automatically make you a better programmer than those using one of
    "those other languages". You can write crap code in any language. You can
    also write great code in just about any language.
</p>
<p>
    Programming takes practice; programming well takes a lot of practice. You
    get better at it by learning more about programming practices, and applying
    them to your language. Part of programming is also learning when a
    particular language is suited to a task, and when it isn't. Python works for
    the web, but it's not particularly suited to it; similarly, you can write
    web servers in PHP, but that doesn't mean you should.
</p>
<p>
    Stop the language wars already! Stop writing incendiary pieces about a
    language you don't use regularly or never gained particular proficiency in,
    and code already!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;