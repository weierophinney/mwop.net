<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('129-MVC-changes-in-Zend-Framework');
$entry->setTitle('MVC changes in Zend Framework');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1165552860);
$entry->setUpdated(1165604782);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Several months ago, <a href="http://andigutmans.blogspot.com/">Andi</a>
    asked me to take the role of lead developer on a refactoring of the 
    <a href="http://framework.zend.com/">Zend Framework</a> MVC components. I
    agreed, though somewhat reluctantly; I already maintain 
    <a href="http://cgiapp.sourceforge.net/">another MVC library</a>, and wasn't
    sure how well I could fill the shoes of people like my friends 
    <a href="http://mikenaberezny.com">Mike</a>, who had done the initial
    development on the controller classes, and 
    <a href="http://paul-m-jones.com/blog/">Paul</a>, who provided Zend_View.
</p>
<p>
    The experience has been incredibly rewarding, however, and I've had the
    chance to pick the brains of and work with some top-notch developers in the
    process. In the next week or so, we'll be releasing version 0.6.0 of the
    framework, and it will include much of my work in the MVC components as part
    of the core distribution. A big thanks to all those who have contributed
    opinions, design help, code, tests, and documentation; another thank you
    goes to Andi for trusting and supporting me in this endeavor.
</p>
<p>
    So, what are the changes? Read on to find out...
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    Most of the changes lie under the hood at this point; the most basic usage
    of the MVC components remains the same. The changes introduced mainly
    promote greater flexibility and testing. In fact, the most significant
    changes, the introduction of request and response objects, are transparent
    to most users, but are the very changes that make unit testing of the MVC
    components both possible and easy.
</p>
<p>
    So, as you may have guessed, the controller classes are now unit tested;
    coverage isn't perfect, but it's reasonable (currently around 68% code
    coverage), and an improvement over the basically zero coverage from before
    (only Martel's RewriteRouter had tests before I started). What this means to
    developers is that from this point forward, any changes to the controller
    classes will need to pass regression tests... and those tests actually
    exist. It also means that it is now possible to unit test applications
    without needing a web server; this is going to be a big step for MVC
    applications based on the framework.
</p>
<p>
    By introducing the request and response objects, we now no longer need to
    rely on a web environment in order to make requests; having a response
    object makes it trivial to capture output from the various action
    controllers and then test against expected results. This is a huge change.
</p>
<p>
    Additionally, by de-coupling the controllers from the request environment,
    it becomes possible to use the MVC components in non-web environments. Think
    CLI and PHP-GTK.
</p>
<p>
    Another change is that a router is no longer needed. This allows developers
    to use the MVC components in non-web environments, where routing may not
    actually be necessary, as well as to use the MVC components in web
    environments where pretty URLs are difficult to configure. IIS, for
    instance, doesn't have a mod_rewrite equivalent out-of-the-box, so being
    able to specify a url such as
    <kbd>http://localhost/index.php?controller=index&amp;action=view</kbd> and
    have it dispatch properly is a nice feature.
</p>
<p>
    A feature that many were requesting was the ability to push parameters into
    the front controller, and have those push through to each of the router,
    dispatcher, and action controllers. Such an ability would obviate the need
    for a registry, and also allow the entire controller chain to share an
    environment. I made use of this today when a request came in for the ability
    to specify an optional module parameter in the request URI; instead of
    breaking backwards compatability or creating new routers and dispatchers, I
    was able instead to simply push a 'useModules' setting through the chain,
    and if discovered, act on it. (This new feature allows urls such as
    <kbd>http://localhost/module/controller/action</kbd> to dispatch to
    Module_Controller::actionAction(); think controller classes in
    subdirectories.)
</p>
<p>
    One feature that I think a lot of people don't understand is the Response
    object. It is a container for the entire response generated by a request,
    whether that's from a single or multiple actions. As such, it also
    aggregates exceptions from the process. It's final purpose is to return that
    response to the client, and this is done by simply echo()ing it; its
    __toString() method should take care of any final rendering to perform.
</p>
<p>
    The basic response object provided with the new system does very little. It
    allows for the setting and aggregation of content through its setBody() and
    appendBody() methods, as well as setting headers via setHeader(). If
    exceptions occur, they are registered via setException(). The suggestion
    made in the documentation is to use appendBody() to aggregate content, and
    then have __toString() return the aggregated content en masse by echoing the
    response at the end of the dispatch loop: echo $front->dispatch().
</p>
<p>
    While this is nice, there are some much more interesting things you can do
    with it. In a recent project I did, I integrated Zend_View and Zend_Json in
    the response object, and added accessors in my action controllers to push
    content to the response object. Then, based on the request, I could switch
    between returning JSON strings or XHTML content. If an exception occurred, I
    could redirect to an error page, or, in the case of an AJAX request, return
    an error encapsulated in a JSON string.  This type of context switching is
    very powerful, and I'll blog more about how it can be achieved later.
</p>
<p>
    If you already use the framework MVC but haven't tried the new code, I
    encourage you to <a href="http://framework.zend.com/download/snapshot">download a snapshot</a> 
    or grab it from <a href="http://framework.zend.com/wiki/x/IgE">subversion</a>
    and give it a spin; there's 
    <a href="http://framework.zend.com/wiki/display/ZFDOCDEV/Migrating+from+Previous+Versions">a document covering migration</a>,
    and any feedback or additions on this would be greatly appreciated (this
    document in the framework wiki is currently out-of-date; check the docbook
    in the framework distribution you download for more current information).
</p>
<p>
    If you haven't tried the framework MVC, and are interested in MVC libraries,
    give it a whirl and let me know what you think!
</p>
EOT;
$entry->setExtended($extended);

return $entry;