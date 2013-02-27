<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2013-02-27-resigned-from-php-fig');
$entry->setTitle('On PHP-FIG');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(false);
$entry->setCreated(new \DateTime('2013-02-27 12:41', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2013-02-27 12:41', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
    'personal'
));

$body =<<<'EOT'
<p>Yesterday, I left the PHP-FIG group.</p>

<p>As in: left the github organization I created, and removed myself from the
mailing list I created.</p>

<p>I have contacted members of my development team and the Zend Framework community
review team to see if anybody is willing to represent ZF in the group. I no
longer am.</p>

<p>I was going to leave quietly, but as a favor to Paul M. Jones -- a good friend
and sometimes collaborator -- I'm writing now.</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>I had high hopes for the group. It was the culmination of something I've been
ruminating on for almost a decade (<a href="http://www.mwop.net/blog/12-PHP-standards-ruminations.html">see post number 12 on my blog, dated to
January 2004, for
proof</a>). My thoughts
have mainly been around coding standards and best practices, helping educate
developers around their benefits, and how to leverage both in order to
create maintainable code.</p>

<p>First, a few thoughts:</p>

<ul>
<li>I personally feel that interfaces are a bad fit for the organization;
<a href="http://www.mwop.net/blog/2012-12-20-on-shared-interfaces.html">I have outlined my thoughts on interface standardization
elsewhere</a>.</li>
<li>Multiple coding standards are okay. A standard for every project,
organization, or developer is not. The ideal is a handful or so for any given
language, as more than that means there are no standards; it's just a
free-for-all.</li>
<li>No individual coding standard will satisfy all developers. In fact, in my
experience, there will always be choices in <em>any</em> standard that even the
authors of the standard are unhappy with. The point of a coding standard is
not to make everyone happy. It's to have a document that details the structure
of code so that developers focus on the intent of code, not the way it's
formatted.</li>
<li>Coding standards are useless if they do not contain hard rules, as automated
tooling to sniff for CS issues cannot be written. "COULD", "SHOULD", and "CAN"
are all problematic, and any use of "EITHER" is going to make automation
ridiculously complex.</li>
<li>In the end, no matter what the technical arguments are for any given detail,
all coding standards are ultimately subjective. The only objective standard is
what is parseable in the given language.</li>
<li>What matters is that you <em>adopt</em> an <em>existing</em> standard, and <em>use</em> it. When
you do, you can automate some code review, prevent developer commit skirmishes
arising from differences in formatting aesthetics, and focus on the problem
you're trying to solve in your code.</li>
</ul><p>With those thoughts as background, then, I can better explain my departure.</p>

<p>The point of PHP-FIG was to create consensus around practices shared by its
member groups, no more, no less.</p>

<p>When PSR-0 was created, we had around a half-dozen member groups. You have to
start somewhere.</p>

<p>Each proposal since then has had an increasing number of members both
discussing and voting on proposals. That means the early proposals may not be
representative of the later membership. That's a simple fact.</p>

<p>That does not mean the standards should <em>change</em>. Once published, a standard is
done. The only thing that can happen is that a <em>new</em> standard may be created
that can <em>supersede</em> an existing standard, or be used <em>instead</em> <em>of</em> an existing
standard. As examples from existing standards bodies, consider RFC 822, which
codified the format of internet text messages (email); it superseded at least
one other RFC, and has itself been superseded twice (in RFC 2822 and RFC 5322).</p>

<p>PHP-FIG adopted the same workflow. If new practices emerge, or the makeup of the
organization significantly changes, and existing recommendations are found to be
obsolete or outdated, a <em>new</em> recommendation may be proposed to supersede or be
adopted in parallel to them. </p>

<p>Parallel standards from the same body, however, should be considered <em>very</em>
carefully, as they lead to splintering and fragmentation of the standards body
and member organizations. If consensus cannot be achieved, why bother?</p>

<p>What I see happening in the PHP-FIG github organization (in pull request comment
threads) and google group, however, is the exact opposite of the goals that
originally led to the group being formed. Instead of people trying to achieve
consensus, I see a lot of polarizing, all-or-nothing arguments occurring, often
over very subjective things. Developers are defending their opinions and
viewpoints with little to no real analysis of what others present.  I see a lot
of "you're wrong" types of remarks. These are coming largely from non-voting
members, but it means the signal-to-noise ratio within these forums is skewed;
it's hard to find reasonable discussion occurring due to this behavior.</p>

<p>The "old guard" is guilty of this at times, too -- but not as often as many
might insist. The insistence of folks like Paul M Jones, Paul Dragoonis, and
Lukas Kahwe Smith that discussion should happen on-list, and that PRs are not
considered for existing, accepted standards is actually reasonable -- for the
reasons I outlined above. A standard is written once; if it needs revision, a
<em>new</em> standard should be written superseding it.</p>

<p>What is most irritating, however, is it doesn't matter how many times such
statements are made; people insist on debating their pet peeves on existing
standards without following the guidelines and established process -- nor
listening when others point out that the debate has occurred before, will occur
again, and needs to stop for now so that we can focus on constructive tasks.
I see a lot of name calling, a lot of accusations of a "dictatorship" (they're
<em>recommendations</em> people; they're not <em>requirements</em>), and overall egotistical
behavior.</p>

<p>I'm tired of it. I have better things to do with my time, things I want to
create, software I want to support, hobbies and interests I want to pursue.
Debating brace placement, tabs vs spaces (for the umpteenth time), or whether or
not annotations have a place in programming in a dynamic language? Not so much.</p>

<p>I hope PHP-FIG can achieve the goals it started with. It will have to do so
without my participation, though.</p>

<h4>Note</h4>

<p>
    I've disabled comments on this post.
</p>

EOT;
$entry->setExtended($extended);

return $entry;
