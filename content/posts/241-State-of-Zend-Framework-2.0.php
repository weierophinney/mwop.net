<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('241-State-of-Zend-Framework-2.0');
$entry->setTitle('State of Zend Framework 2.0');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1275660000);
$entry->setUpdated(1276198994);
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
    The past few months have kept myself and my team quite busy, as we've turned
    our attentions from maintenance of the <a
        href="http://framework.zend.com">Zend Framework</a> 1.X series to Zend
    Framework 2.0. I've been fielding questions regularly about ZF2 lately, and
    felt it was time to talk about the roadmap for ZF2, what we've done so far,
    and how the community can help.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Zend Framework 2.0 Roadmap</h2>

<p>
    2.0 marks the first new major release of Zend Framework, and, as such, is
    the first time we can break backwards compatibility. Major releases are
    generally of two flavors: large, new featuresets, or rewrites and
    refactoring to fix architectural concerns. ZF2 falls primarily in this
    latter category.
</p>

<p>
    I posted a rough roadmap in November on the ZF wiki, to which we received a
    lot of feedback. Several ideas we brought up that were contested, and we
    re-considered many of the decisions and goals we outlined as a result before
    we started implementation.
</p>

<p>
    Also, around 5 or 6 weeks ago, I started a discussion with <a
        href="http://karwin.blogspot.com/">Bill Karwin</a>, who led the project
    from mid-2006 through the 1.0 release in 2007 and slightly beyond. He had
    some solid feedback on the nature of the roadmap, and with this information,
    results of a poll we did late last year, and feedback I've had via mailing
    lists, IRC, twitter, blogs, and more, I <a
        href="http://framework.zend.com/wiki/display/ZFDEV2/Zend+Framework+2.0+Requirements">published
        a new roadmap</a> that focussed less on implementation detail while
    firmly and succinctly stating the requirements for the project.  
</p>

<p>
    Stated in a sentence:
</p>

<blockquote>
    The primary thrust of ZF 2.0 is to make a more consistent, well-documented
    product, improving developer productivity and runtime performance.
</blockquote>

<p>
    The basic goals are as follows:
</p>

<ul>
    <li>Ease the learning curve</li>
    <li>Make extending the framework trivially simple</li>
    <li>Improve baseline performance of the framework</li>
    <li>Simplify maintenance of the framework</li>
    <li>Be an exemplar of PHP 5.3 usage</li>
    <li>Provide mechanisms for using just the parts of the framework needed</li>
</ul>

<p>
    We also stated several general development objectives for those contributing
    to ZF2:
</p>

<ul>
    <li>Simplify</li>
    <li>Programming by Contract</li>
    <li>Favoring the Explicit</li>
</ul>

<p>
    For more detail on each of these goals, I encourage you to <a href="http://framework.zend.com/wiki/display/ZFDEV2/Zend+Framework+2.0+Requirements">read
        the document</a>.
</p>

<h2>What has been accomplished</h2>

<p>
    While the roadmap has only really stabilized recently, that does not mean we
    haven't been working steadily on its development. There were some objectives
    we anticipated as early as 2 years ago. Among these were migrating the
    project to <a href="http://php.net/namespace">namespaces</a>, providing
    infrastructure to allow cherry-picking components for packaging, and
    updating the unit test infrastructure to make better use of more recent <a
        href="http://phpunit.de/">PHPUnit</a> features.
</p>

<p>
    Shortly after <a
        href="http://devzone.zend.com/article/11727-Zend-Framework-1.10.0-STABLE-Released">1.10.0
    was released</a>, I created a temporary git repository on my own server, and
    started work. The first task I did was to update the unit test suite and
    analyze all class files for dependencies to assist in the namespaces
    migration.
</p>

<p>
    After completing this process, my entire team -- all three of us -- started
    the work of migrating the code to namespaces. <a
        href="http://ralphschindler.com">Ralph</a> wrote a tool that scanned the
    library and created a map file of existing classes and suggested
    namespace/classname combinations. We then used this tool as a launching
    point for the migration, each of us working on a component at a time. This
    work was by no means automated -- we discovered very quickly that such a
    tool only took care of the most cursory work. <a href="http://weierophinney.net/matthew/archives/237-A-Primer-for-PHP-5.3s-New-Language-Features.html">I detailed some of
    our findings a couple months back</a>; we ran into a number of issues we
    never anticipated, and the progress has been far from speedy. At this point,
    however, we have migrated everything but the <code>Zend_Service</code>
    classes, the MVC, and those components that build on top of the MVC
    (Application, Navigation, Form, etc.).
</p>

<p>
    We also rewrote a few components during this time, as we discovered
    inconsistencies or in areas where we had problems with unit testing. One
    such is a component that has been a pain point basically since its creation:
    Zend\Session. The new design gives a good idea of what can be accomplished
    during a focussed rewrite, and by using 5.3 features where they make sense,
    and I'm very pleased with how it turned out.
</p>

<p>
    In parallel with this effort, I also did a fair bit of research determining
    how we would offer our <a href="http://git-scm.org/">Git</a> repository and
    workflow. We're going for a fairly traditional workflow where only a small
    handful of developers will have commit access, and all other contributors
    will submit pull requests to those developers -- for everything ranging from
    documentation fixes to bugfixes and feature topics. To ensure that those
    contributing have signed a CLA, we have created a <code>pre-receive</code>
    hook that verifies either the author or reviewer against a list of CLA
    signees. Additionally, we have created <code>post-receive</code> processes
    to create RSS feeds and deliver email notifications. These processes will be
    easy for us to hook into to add new functionality -- such as sending
    updates to a twitter account, <a
        href="http://weierophinney.net/matthew/archives/240-Writing-Gearman-Workers-in-PHP.html">performing subtree
        merges</a>, and more. This should aid us greatly in setting up continous
    integration in the near future.
</p>

<p>
    The official Zend Framework 2.0 Git repository is available for cloning:
</p>

<ul>
    <li><code>git clone git://git.zendframework.com/zf.git</code></li>
</ul>

<p>
    The helpful folks at <a href="http://github.com/">Github</a> have also
    kindly provided a <a href="http://github.com/zendframework/zf2">mirror of
        the repository</a>; our hope is that contributors can fork from there in
    order to collaborate on new features and bug fixes. <em>(I've also cloned it
        <a href="http://github.com/weierophinney/zf2">under my own Github
    account</a>, for those who want to issue pull requests.)</em>
</p>

<p><em>
    Warning! Zend Framework 2.0 development is in <strong>very</strong> early
    stages, and should <strong>not</strong> be used for developing production
    applications. In fact, the APIs <strong>will</strong> change in the coming
    weeks and months, and should not be relied on for really much of anything.
</em></p>

<h2>Community Initiatives</h2>

<p>
    A number of new initiatives have sprung up in the last week surrounding the
    community's involvement in the ZF2 process. 
    
<p>
    The primary goal of my team has been to get the library migrated
    sufficiently so that we can open the repository to cloning, and allow
    contributors to begin working on initiatives to improve the framework. This
    is now possible, and several other initiatives are emerging.
</p>

<p>
    Several community members have put forth the idea of a community review
    team. This effort is still taking shape, but the basic goals are: 
</p>

<ul>
    <li>Assist contributors in getting patches into the framework, primarily by
    acting as a liaison to missing maintainers or arbitrators between
    maintainers and other contributors.</li>

    <li>Shepherd new feature proposals into the master branch, by performing
    proposal review and code review.</li>
</ul>

<p>
    For more information on this effort, please <a
        href="http://zend-framework-community.634137.n4.nabble.com/Community-Review-Team-tp2242135.html">review
    the thread on the zf-contributors mailing list</a>.
</p>

<p>
    A number of contributors are also starting to discuss rewrites and
    refactoring of components. Much of this is being done on the
    <code>zf-contributors</code> mailing list, and some on the #zftalk.dev
    channel on Freenode. If you are interested in contributing, I highly
    recommend subscribing to the list and dropping into the channel when you
    can.
</p>

<h2>End Notes</h2>

<p>
    These are exciting times for Zend Framework development -- the first time we
    can break backwards compatibility since the 1.0 release, and a chance to
    participate in cutting edge PHP 5.3 development. While the process has been
    slow, it's also been incredibly rewarding and a huge learning experience --
    and I'm glad I've had the chance to participate. I hope you'll join us!
</p>
EOT;
$entry->setExtended($extended);

return $entry;