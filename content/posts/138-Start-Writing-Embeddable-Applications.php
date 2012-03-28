<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('138-Start-Writing-Embeddable-Applications');
$entry->setTitle('Start Writing Embeddable Applications');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1178510440);
$entry->setUpdated(1178669778);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    <a href="http://killersoft.com/randomstrings/">Clay Loveless</a> wrote last
    year an article entitled <a
        href="http://killersoft.com/randomstrings/2006/06/14/stop-writing-loner-applications/">Stop
        Writing Loner Applications</a> in which he ranted about all the
    monolithic applications that act like they're the only kid on the block when
    it comes to user authentication. Basically, if you want to create a site
    that utilizes several third-party, off-the-shelf PHP apps (say, a forum, a
    blog, and a wiki), getting a shared authentication to work between them can
    be more than a little painful.
</p>
<p>
    I've hit a similar problem repeatedly the past couple months: most of these
    apps simply are not embeddable, at least not without modifying the source.
</p>
<p>
    "Why embed?" you ask. Simple: if I'm creating a site that has one or two of
    these applications, but also my (or my company's) own custom functionality,
    I may want to ensure that certain elements are present on all pages, or that
    I can control some of the content in all pages: a unified header and footer,
    ability to inject statistic tracking javascript, etc.
</p>
<p>
    The predominant attitudes are either, "Don't embed our app, embed your app
    in ours," or "Just modify the templates." Neither of these solutions is
    acceptable.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    Why not? In the first case, it's <em>my</em> site. If I'm mixing and
    matching several such applications, which ones should I embed, and which
    should be the master? Honestly, the applications <em>I'm</em> writing for
    the site are the master application; the third party solutions should be
    embedded in <em>my</em> website.
</p>
<p>
    In the second case, I may have my own header and footer, and tools for
    automating what tracking scripts are embedded when -- in other words, I'm
    running my own display logic, possibly with my own tools.  Embedding these
    tools into another apps templates is at times difficult (if the application
    is simply using PHP, the difficulty may be mainly finding what code to
    alter), at times impossible (if the application uses a templating engine
    vastly different than what I'm using, or one that does not allow arbitrary
    PHP). Why should I have to write an interface to my code for each
    application?
</p>
<p>
    Truthfully, it simply makes sense to use a Two Step View in most cases,
    having the application generate content that is then injected into a
    sitewide template I control.
</p>
<p>
    I've tried in a number of cases to write wrappers so I can grab content from
    these third party apps, typically using output buffering to capture the
    output so I can inject it into my own views. So far, my experience has been
    universally dismal. Most of the secure, robust apps out there (I'm not going
    to name names) <em>still</em> use procedural methods for at the very least
    the main script, usually index.php. This includes slurping in configuration
    from other files... all of which happens in the global namespace. What's the
    problem?  Most wrappers I write are by necessity class methods or functions,
    or run from within one, meaning the global namespace is no longer in effect.
    The end result is that I have to greatly alter the code to get things to
    work -- in one case, my colleague and I ended up changing all $_GLOBALS
    references to $_SESSION simply to get things to work. Hackish, but it got
    the job done.  However, it also means it will be a nightmare to upgrade
    until we can script it.
</p>
<p>
    If you're writing a standalone PHP application, maybe the next great forum
    software, or blog software, or wiki, or what have you, please design it in
    such a way that it is easily embeddable: 
</p>
<ul>
    <li>
        When using configuration files, use a configuration component that
        doesn't require use of the global namespace (PEAR's Config, Solar's
        Solar_Config, and Zend Framework's Zend_Config come to mind); when
        coupled with a registry or implemented as a static class property (in
        PHP5), you can have access to the configuration from anywhere in your
        application. 
    </li>
    <li>
        Have your bootstrap script call on class methods or functions to do
        their work. Don't do any decisioning in the global namespace.
    </li>
    <li>
        Better yet, use an MVC pattern in your apps, and have your bootstrap
        simply dispatch the controller. This can easily be duplicated in
        somebody else's code, or simply directly included.
    </li>
    <li>
        Make sure your templates are easily modified to allow developers to
        strip out header, footer, and menu elements.
    </li>
    <li>
        Create an API to allow retrieving necessary javascript and CSS so that
        it can later be injected into another system's templates.
    </li>
    <li>
        Don't use $_GLOBALS ever. It seems like an easy way to keep variables
        accessible across classes and functions, but with PHP 5's static
        properties, or judicious usage of singleton's in PHP 4, there are other
        ways to accomplish the same effect with fewer side effects.
    </li>
</ul>
<p>
    If you're responsible for maintaining an existing project, please start
    fixing your application today so it can be embedded. Believe it or not, it
    may actually <em>increase</em> adoption of your project, as more people will
    be able to use it within their existing sites. At the very least, you'll
    stop me from ranting, and reduce the amount I spend on acetaminophen.
</p>
EOT;
$entry->setExtended($extended);

return $entry;