<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('1-Zippity-Zoo-Zah...');
$entry->setTitle('Zippity Zoo Zah...');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1111717686);
$entry->setUpdated(1111719690);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
I've been wanting to improve -- no, rewrite -- my blogging software for a month or more now. The more work I've done at work, the more efficiently I've felt I could handle it. I've learned a lot of ins and outs with Cgiapp and Cgiapp superclasses, and figured out how to tie things together much better.

However, I've also noticed, in reading and analyzing blogs, that there are a lot of standards that I just don't know enough about or simply don't have the time to implement ("the more work I've done..." has been something like 70 hour weeks recently). Things like trackbacks, and RSS/Atom feeds, and comment moderation/blacklisting, etc. I certainly have the skills, but do I have the time? Not really.

I still have a goal of writing some lightweight blogging software using Cgiapp. However, for now, that's on hold so I can focus on family, myself, and the next round of Cgiapp improvements. 

In the meantime, i've been seeing a lot of stuff about <a href="http://www.s9y.org/">Serendipity</a>, and I thought I'd give it a try. Here are the results!

It was super fast to setup, and I hope to import all my old entries from my own system to it, once I can see how entries are stored in the DB.

So, you can now keep track of me via RSS... Read away!

<b>Update:</b> I'm liking S9y even more: it uses <a href="http://smarty.php.net/">Smarty</a> for templating, which means I'm able to modify it to look like it was always seemlessly in my site!
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;