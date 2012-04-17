<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->fromArray(array(
    'id'    => 'matthew',
    'name'  => "Matthew Weier O'Phinney",
    'email' => 'me@mwop.net',
    'url'   => 'http://mwop.net/',
));

$entry = new EntryEntity();

$entry->setId('255-How-to-Contribute-to-ZF2');
$entry->setTitle('How to Contribute to ZF2');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1299249000);
$entry->setUpdated(new DateTime('2012-04-17 10:45:00', new DateTimezone('America/Chicago')));
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
ZF2 development is ramping up. We've been at it for some time now, but mostly
taking care of infrastructure: converting to namespaces, re-working our
exception strategy, improving our test suites, and improving our autoloading and
plugin loading strategies to be more performant and flexible. Today, we're
actively working on the MVC milestone, which we expect to be one of the last
major pieces necessary for developers to start developing on top of ZF2.
</p>

<p>
A question I receive often is: "How can I contribute to ZF2?"
</p>

<p>
Consider this your guide.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2 id="toc_1.1">Getting Setup</h2>

<p><del>
Just like ZF1, ZF2 requires the same
<a href="http://framework.zend.com/cla">Contributors License Agreement (CLA)</a>. This
agreement helps protect end users from litigation; basically, you're ensuring
that you are the author of the code you contribute, or that you have secured the
rights to any code you contribute. If anybody contests the origin, they can
legally only approach you, not the end users.
</del></p>

<p><del>
You can either FAX your signed CLA, or you can scan and email it to us; we
prefer the latter (email).
</del></p>

<p>
<del>As part of the CLA submission process, d</del>
</p>

<p><em>Note: a CLA is no longer necessary for ZF2 contribution!</em></p>

<p>
Don't forget to sign up for an account on
our <a href="http://framework.zend.com/issues">issue tracker</a> if you haven't already;
if you have, make sure your email address is current and correct (you can do so
via our <a href="http://framework.zend.com/crowd">Crowd instance</a>).
</p>

<p>
ZF2 development is not using the same Subversion repository as ZF1. Instead,
we've switched to <a href="http://git-scm.org/">Git</a> for our version control needs; the
distributed nature of our ZF contributors lends itself well to a distributed
VCS, and Git was the VCS that our contributors were most familiar with.
</p>

<p>
While we are hosting our own Git repository, the kind folks at
<a href="http://github.com/">GitHub</a> have set up a mirror of the repository that is
synced once or twice a day. As such, we encourage developers to utilize GitHub
to host their repositories.
</p>

<p>
You can find the ZF2 Git repository at <a href="http://github.com/zendframework/zf2">http://github.com/zendframework/zf2</a>,
along with instructions on forking the repository.
</p>

<p>
Once you have forked and cloned the repository locally, please update your git
configuration to ensure your author email address matches that in our issue
tracker:
</p>

<div class="example"><pre><code lang="bash">
&gt; cd zf2
&gt; git config set user.email &lt;email&gt;
</code></pre></div>

<p>
The above information is also <a href="http://bit.ly/zf2gitguide">available in detail on our wiki</a>.  
</p>

<h2 id="toc_1.2">Conventions</h2>

<p>
Now that you're ready to contribute, we have a few conventions.
</p>

<p>
First, each discrete bugfix or feature change should be done in a separate
branch of your repository. This makes it simpler to evaluate and review changes.
</p>

<p>
My suggestions for naming these branches are as follows:
</p>

<ul>
<li>
"hotfix/&lt;Issue ID&gt;", where "&lt;Issue ID&gt;" is the ID from the
   <a href="http://framework.zend.com/issues">ZF issue tracker</a>; e.g.,
   "hotfix/ZF-10989". Use this format for bugfixes.
</li>
<li>
"feature/&lt;featurename&gt;", where "&lt;featurename&gt;" is a short yet descriptive
   name of the developed feature; e.g., "feature/translate_resource_es". Use
   this format for feature changes or new features.
</li>
</ul>

<p>
In particular, using the issue tracker ID for the bugfixes helps a ton in
evaluating if the fix is appropriate for the reported issue.
</p>

<p>
Next, for all code changes -- be they bugfixes, feature changes, or new features
-- include unit tests. We will throw a pull request back your way immediately if
it does not include tests.
</p>

<p>
Once you've completed your bugfix or feature request, issue us a pull request.
Again, GitHub makes this dead-simple. Make sure when you create the pull
request that you give a good, succinct title, and adequate detail in the message
describing what you've done; this makes review and prioritization easier.
</p>

<h2 id="toc_1.3">Where we are currently</h2>

<p>
Last year, we drafted our <a href="http://bit.ly/zf2reqs">requirements</a>, as
well as a list of <a href="http://bit.ly/zf2milestones">milestones</a>. They include:
</p>

<ul>
<li>
Autoloading and Plugin Loading (improvements and additions to autoloading and
   plugin loading, as well as making these consistent throughout the framework)
</li>
<li>
Exceptions (updated exception strategy to make it more flexible and remove
   dependencies)
</li>
<li>
Testing (consistent infrastructure, better use of modern PHPUnit features,
   etc.)
</li>
<li>
MVC (more flexible strategies and improvements to architecture)
</li>
<li>
Internationalization and Localization (performance and architectural
   optimizations)
</li>
<li>
Documentation (consistent structure, including more examples and detailing
   all configuration options and methods)
</li>
</ul>

<p>
To date, we've completed the following milestones:
</p>

<ul>
<li>
Autoloading and Plugin Loading
</li>
<li>
Exceptions
</li>
</ul>

<p>
We've <a href="http://framework.zend.com/wiki/display/ZFDEV2/Proposal+for+Documentation+in+ZF2">proposed a documentation structure</a>, but, obviously, have not completed the Documentation milestone (I expect this to be one of the last to be completed, though it should be accomplished in parallel with other milestones).
</p>

<p>
Additionally, we've worked extensively on our testing infrastructure, but have a
few changes yet to make the structure uniform and cohesive.
</p>

<p>
We're currently working on the MVC milestone, and have a set of <a href="http://framework.zend.com/wiki/display/ZFDEV2/Proposal+for+MVC+Interfaces">MVC interface proposals</a> complete and accepted; development of proposed
implementation is in progress, and we will likely have further proposals in the
coming weeks, as well as specific tasks the community can assist us with.
</p>

<h2 id="toc_1.4">What you can work on now</h2>

<p>
Much of the current work is being spear-headed by Zend's ZF team, for which I am
Project Lead. However, there's plenty to work on:
</p>

<ul>
<li>
The community maintains a list of
      <a href="http://framework.zend.com/wiki/display/ZFDEV2/Component+Maintainers">component maintainers</a>. 
      If you're interested in working on a component, contact the maintainer or
      any listed developers, and discuss the direction with them. If you can't
      reach anyone, or the component has no listed maintainers, offer to take
      over maintenance.
</li>
<li>
Most <em>service components</em> currently need to be migrated to namespaces.
      These are listed on the same page linked above, and are an excellent place
      to start.
</li>
<li>
If nothing else, just running individual component test suites and helping
      fix testing issues is always a huge help.
</li>
<li>
Review the <a href="http://framework.zend.com/wiki/display/ZFDEV2/Proposal+for+Documentation+in+ZF2">proposed documentation standard</a>, and start updating the documentation.
</li>
</ul>

<h2 id="toc_1.5">Thank You!</h2>

<p>
To those of you who take the plunge and start contributing, I extend an early
thank you! The efforts of our contributors are what make the framework
compelling for developers!
</p>

<h2>Update</h2>
<ul>
    <li>Fixed all links - thanks for the reports!</li>
    <li><b>2012-04-17</b>: struck out CLA info; no longer required</li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;
