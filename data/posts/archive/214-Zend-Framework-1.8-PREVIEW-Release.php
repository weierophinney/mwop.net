<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('214-Zend-Framework-1.8-PREVIEW-Release');
$entry->setTitle('Zend Framework 1.8 PREVIEW Release');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1239157673);
$entry->setUpdated(1239661848);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
));

$body =<<<'EOT'
<p>
    By the time you read this, the <a href="http://framework.zend.com/">Zend Framework</a> 
    team will have released a <strong>preview</strong> release of 1.8.0. While
    the final release is scheduled for later this month, this release represents
    the hard work of many contributors and shows off a variety of powerful new
    components.
</p>

<p>
    If you're a Zend Framework user, you should give the preview release a spin,
    to see what it can do:
</p>

<ul>
    <li><a href="http://framework.zend.com/releases/ZendFramework-1.8.0a1/ZendFramework-1.8.0a1.zip">1.8 Preview Release
        (zip)</a></li>
    <li><a href="http://framework.zend.com/releases/ZendFramework-1.8.0a1/ZendFramework-1.8.0a1.tar.gz">1.8 Preview Release
        (tarball)</a></li>
</ul>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    One common criticism of Zend Framework is that it doesn't fulfill the
    traditional definition of a framework. One definition cited has been from 
    <a href="http://www.thefreedictionary.com/framework">TheFreeDictionary</a>,
    and includes the following two potential matches:
</p>

<blockquote>
    A structure for supporting or enclosing something else, especially a
    skeletal support used as the basis for something being constructed.
</blockquote>

<blockquote>
    A set of assumptions, concepts, values, and practices that constitutes a way
    of viewing reality.
</blockquote>

<p>
    The argument is that ZF does not provide the "assumptions" or opinions on
    how an application should be built. However, this makes sense only if you
    buy into the idea that a framework should always follow the "convention over
    configuration" rule -- which we soundly reject with Zend Framework. Our
    opinion has always been that developers know best how their application
    should be built, and that ZF code should support the myriad uses to which
    they will put it.
</p>

<p>
    That said, with the addition of <a href="http://framework.zend.com/manual/en/zend.application.html">Zend_Application</a> 
    and
    <a href="http://framework.zend.com/manual/en/zend.tool.framework.html">Zend_Tool</a>, 
    Zend Framework now provides a comprehensive framework for its users that is
    opinionated <em>and</em> provides the flexibility for developers to impose
    whatever structure they need.
</p>

<p>
    <code>Zend_Tool</code> provides a tooling framework for Zend Framework. It
    allows you to create your own tooling providers that can then be utilized by
    tooling clients, which utilize an RPC style architecture. We now ship a
    Console or command line interface (CLI) client that allows you to perform a
    variety of tasks -- such as setting up your initial project structure,
    adding new resources to a project, adding action methods and view scripts to
    controllers, and more. As an example, you can now do this:
</p>
    
<div class="example"><pre><code lang="sh">
% zf create project foo
</code></pre></div>

<p>
    and generate the skeleton for a new project in a directory named "foo", with
    the following structure:
</p>

<div class="example"><pre><code lang="txt">
|-- application
|   |-- Bootstrap.php
|   |-- configs
|   |   `-- application.ini
|   |-- controllers
|   |   |-- ErrorController.php
|   |   `-- IndexController.php
|   |-- models
|   `-- views
|       |-- helpers
|       `-- scripts
|           |-- error
|           |   `-- error.phtml
|           `-- index
|               `-- index.phtml
|-- library
|-- public
|   |-- .htaccess
|   `-- index.php
`-- tests
    |-- application
    |   `-- bootstrap.php
    |-- library
    |   `-- bootstrap.php
    `-- phpunit.xml
</code></pre></div>

<p>
    In the future, we will be adding more support to
    this. A big kudos to <a href="http://ralphschindler.com/">Ralph
        Schindler</a> for doing the heavy lifting on this project.
</p>

<p>
    <code>Zend_Application</code> provides both bootstrapping of your PHP
    environment as well as your application environment. When using
    <code>Zend_Application</code>, you will create an application bootstrap
    class that can either use resource plugin classes or define initialization
    routines internally; regardless, it allows you to define resource
    dependencies and bootstrap the various facets of your application. Even
    better, it introduces modules as first-class citizens of your applications.
    With the introduction of <code>Zend_Loader_Autoloader_Resource</code> and
    <code>Zend_Application_Module_Autoloader</code>, you can now use autoloading
    to resolve the various resource classes in your modules -- such as models,
    forms, and plugins. This tremendously simplifies the story for utilizing
    resources from other modules, as well as using resources within the same
    module. A big thank you goes out to 
    <a href="http://www.dasprids.de/">Ben Scholzen</a> for getting the ball
    rolling on <code>Zend_Application</code> and his significant contributions
    to the component.
</p>

<p>
    There are many other stories in this release:
</p>

<ul>
    <li>Amazon EC2 and S3 support (contributed by 
        <a href="http://www.bombdiggity.net/">Jon Whitcraft</a> and 
        Justin Plock/<a href="http://php100.wordpress.com/">Stas Malyshev</a>, 
        respectively)</li>
    <li>Zend_Navigation, a comprehensive solution to generating and organizing
        navigation elements for use with breadcrumbs, navigation menus,
        sitemaps, and more (contributed by Robin Skoglund and Geoffrey Tran,
        from <a href="http://www.zym-project.com/">Zym</a>)</li>
    <li></li>
    <li>Numerous additions to Zend_Validate and Zend_Filter support (primarily
        by <a href="http://www.thomasweidner.com/flatpress/">Thomas Weidner</a>)</li>
    <li>Improvements to Zend_Search_Lucene support including searching multiple
        indexes and keyword field search via query strings (contributed by
        Alexander Veremyev)</li>
    <li>Improvements to Zend_Pdf, including page scaling, shifting, and skewing
        (contributed by Alexander Veremyev)</li>
    <li>and more...</li>
</ul>

<p>
    A hearty thanks to all who have contributed so far in this release. Start
    testing it, and let us know what we can improve for the final 1.8 release
    later this month!
</p>
EOT;
$entry->setExtended($extended);

return $entry;