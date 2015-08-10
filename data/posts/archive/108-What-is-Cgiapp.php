<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('108-What-is-Cgiapp');
$entry->setTitle('What is Cgiapp?');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1146405240);
$entry->setUpdated(1146569829);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    After some conversations with <a href="http://paul-m-jones.com/">Paul</a>
    and <a href="http://naberezny.com/">Mike</a>, in recent months I realized
    that while I often announce new releases of 
    <a href="http://cgiapp.sourceforge.net/">Cgiapp</a>, I rarely explain what
    it is or why I develop it.
</p>
<p>
    <strike>I got into trouble on the <a href="http://pear.php.net/">PEAR</a>
    list when I tried to propose it for inclusion in that project, when I made
    the mistake of describing it as a framework. (This was before frameworks
    became all the rage on the PHP scene; PEAR developers, evidently, will not
    review anything that could possibly be construed or interpreted as a
    framework, even if it isn't.)</strike> I mistakenly called Cgiapp a
    framework once when considering proposing it to PEAR. But if it's not a
    framework, what is Cgiapp? Stated simply:
</p>
<blockquote>
    Cgiapp is the Controller of a Model-View-Controller (MVC) pattern. It can be
    either a front controller or an application controller, though it's
    typically used as the latter. 
</blockquote>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    As a controller, it provides some basic, configurable routing mechanisms to
    determine what should be displayed for a given request, a simple registry
    for configuration variables and for passing variables around between method
    calls, error handling, hooks into a template engine, and hooks into pre/post
    application and request operations.
</p>
<p>
    When developing applications using Cgiapp, you operate on the idea of one
    display screen, one method. You define a hash table mapping display screens
    to methods, and either indicate a query parameter or a segment of the
    request URI that will indicate the display requested; when the request comes
    in, the appropriate method is called. In Cgiapp terms, these are called "run
    modes".
</p>
<p>
    What makes Cgiapp so enticing for me as a developer?
</p>
<ul>
    <li><b>Object orientation.</b> Cgiapp is inherently object oriented; to use
    it, you must create a class that extends Cgiapp (or Cgiapp2!), and map
    methods to actions. There's no getting around it. Because of this,
    applications are namespaced, portable, and testable.</li>
    <li><b>Reusability.</b> Because each application is a class, and each
    application instance is triggered from an instance script, and because you
    may pass configuration variables to the class from the instance script,
    Cgiapp-based applications are inherently reusable. This makes it easy to
    distribute applications, as well as to reuse them in multiple site
    locations. As an example, I have used article applications in multiple
    locations, accessing different article stores -- but using the same article
    application class. Each had a different look and feel, sometimes within the
    same site, sometimes in different sites, but I merely needed to change the
    data store and the templates to achive the differentiation.</li>
    <li><b>Extensibility.</b> Ever code something, and then need an application
    that duplicated much of the functionality, but added some twists to it? One
    such example: I had a small gallery application, and later needed an e-card
    application. The latter was basically a gallery type of application, but
    when selecting an image, needed to display an e-card form, and also needed
    to handle the results of that form. I simply had the e-card class extend the
    gallery class, added a method, and created a new view template for the image
    view. Total extra work: about an hour or so.</li>
    <li><b>Developer freedom.</b> Because Cgiapp is <em>not</em> a framework,
    and, indeed, only a single class (that changes in Cgiapp2, but only because
    Cgiapp2 has some helper classes), it gives a lot of freedom to the
    developer. You can pick and choose what templating system you want to use.
    Or what data storage mechanism you want to use. Or how you'll handle
    sessions. Or whether you'll use pretty urls or GET parameters (or allow one,
    but default to the other). In a nutshell, it allows the developer to cherry
    pick the libraries and components they want to use in their
    application.</li>
</ul>
<p>
    It's the last point, above, that really separates Cgiapp from most other MVC
    frameworks I've reviewed. Most frameworks tend to give you everything,
    package it up, and try to sell it to you as the developer: "Use only the
    tools integrated in our solution, we can't guarantee best performance
    otherwise." Cgiapp doesn't do that. Cgiapp let's the developer call the
    shots and say, "I'm familiar with such-and-such library and want to use that
    in my MVC application," or, "I like such-and-such template engine, and want
    to use that," or, "I need my application to work regardless of whether
    mod_rewrite is available." As examples, I've done extensive development with
    Cgiapp that used Smarty and PEAR::DB; I've also used Savant and Zend_Db. I
    have users that report they love ADODB. I've used pretty URLs, but I've also
    often used GET or POST to determine the current run mode. The point is,
    Cgiapp merely provides an easy to use controller into which you can plug the
    model and view of your choice.
</p>
<p>
    The other very important aspect of Cgiapp, to me, is application
    reusability. With Cgiapp2, this becomes even more of a feature, as
    applications can be customized via run-time plugins from the instance
    script. If an application has to be tied to a particular site structure,
    it's never completely reusable. But when it is configurable per instance,
    via templates, data store, and/or pre/post operation actions, it becomes
    distributable and pluggable. This means the possibility of applications like
    forums, galleries, contact forms, article systems, etc. that can be dropped
    in anywhere in a site and easily configured to match that site's look and
    feel. To me, this is invaluable.
</p>
<p>
    With this explanation of Cgiapp under my belt, I plan to start blogging
    about uses of Cgiapp and Cgiapp2 to show how it can most optimally used. In
    the meantime, feel free to comment and ask questions!
</p>
<p>
    <b>Update:</b> Changed language in third paragraph to put emphasis on Cgiapp
    as <em>not a framework</em> instead of anti-PEAR slant.
</p>
EOT;
$entry->setExtended($extended);

return $entry;