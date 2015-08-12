<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('259-The-Ideas-of-March');
$entry->setTitle('The Ideas of March');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1300214488);
$entry->setUpdated(1300259802);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
<a href="http://shiflett.org/blog/2011/mar/ideas-of-march">Chris Shiflett has asked the question</a>, 
why don't people blog anymore? In this age of real-time streams and
dead-simple status updates, blogs often feel like the uncared-for step-child or
a website; indeed, many folks are trading their blogs for pages that simply
track their various lifestreams (tweets, facebook status, flickr images, and
more). 
</p>

<p>
While this sort of thing is trendy and interesting, it also sucks:
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<ul>
<li>
It's a snapshot of simply what your most recent activity was, and rarely
   provides the ability to mine the streams to get a more full picture.
</li>
<li>
There's no good way to converse. Sure, you can do @mentions, or Facebook
   comments, but it's not the same. These forms are often space-limited, or lose
   context rapidly (ever try to re-create a twitter conversation thread?).
</li>
<li>
What happens when your thoughts are bigger than a tweet?
</li>
</ul>

<p>
I was recently reminded of this in a twitter exchange I had with 
<a href="http://h2ik.co">Jon Whitcraft</a>. He asked for some advice with
<a href="http://git-scm.org">Git</a>, and I replied with some fairly cryptic advice -- which
he simply didn't understand. We then moved the discussion to email, where I was
able to give a full example, with command snippets, project context, etc.
</p>

<p>
In the time I took to write it, I could have written a blog post from which many
folks could have benefitted, and on which they could have commented. (Heck, a
post <strong>I</strong> could go to later when I get stuck with the same issue, as I almost
surely will. Jon was kind enough to
<a href="http://h2ik.co/2011/03/having-fun-with-git-subtree/">post the conversation on his blog</a>.)
</p>

<p>
The point is that Twitter is great for the ephemeral, but a terrible medium for
having a record of ideas.
</p>

<p>
The interesting thing for me about Chris' post is that, in looking back on the
past year, I've already been blogging more. <a href="http://www.zend.com">My employers</a>
like having a post of mine to link to in the monthly newsletter, and having that
(minimal) pressure has forced me to take time each to think about what I've been
working on, what trends I've been observing, or what questions I've been seeing
pop up over and over again... and write. 
</p>

<p>
Writing is an interesting process. It forces one to take those scattered
thoughts and provide some order and structure around them. When done well, it
often helps reveal facets of the subject matter you'd not considered previously,
opening new questions to consider. I find that taking the time to write helps me
fully explore an issue in a way I'd never do normally. As such, my blog has,
believe it or not, been primarily for my own consumption; it's a tool to help me
learn and explore ideas.
</p>

<p>
So, I've tossed my hat in the ring -- now it's your turn: blog it!
</p>
EOT;
$entry->setExtended($extended);

return $entry;