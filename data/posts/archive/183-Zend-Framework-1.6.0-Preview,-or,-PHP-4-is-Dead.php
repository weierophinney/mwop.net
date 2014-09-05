<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('183-Zend-Framework-1.6.0-Preview,-or,-PHP-4-is-Dead');
$entry->setTitle('Zend Framework 1.6.0 Preview, or, PHP 4 is Dead');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1218474900);
$entry->setUpdated(1219047054);
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
    PHP 4 officially died Friday. I started programming PHP with release
    candidates of 4.0.0 -- which simultaneously introduced me to the GNU C
    compiler and toolset. My first official job using PHP was at a shop that was
    using PHP 3, and considering the costs of upgrading to PHP 4 -- which
    clearly offerred many benefits over its predecessor. I switched to PHP 5 as
    soon as the first official release was made four years ago -- the pains of
    reference handling with objects, the introduction of a unified constructor,
    first-class support for overloading, and SimpleXML won me over immediately,
    and I've never looked back. Goodbye, PHP 4; long live PHP!
</p>

<p>
    I'm celebrating with the second release candidate of Zend Framework 1.6.0,
    which should drop today.  There are a ton of new features available that
    I'm really excited about. I'm not going to go into implementation details
    here, but instead catalogue some of the larger and more interesting changes
    that are part of the release.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h3>Dojo Integration</h3>

<p>
    I've <a href="http://weierophinney.net/matthew/archives/176-Zend-Framework-Dojo-Integration.html">blogged</a> <a href="http://weierophinney.net/matthew/archives/178-Zend-FrameworkDojo-Integration-QA-Slides.html">a number of times</a> 
    about this, but a picture is worth a thousand words... or lines of code, in
    this matter. I'm going to show these as images, as I still need to determine
    how and where to get a public demo up, so bear with me. First:
</p>

<a href="/uploads/dojoDemo-2008-08-11.png"><img src="http://weierophinney.net/uploads/dojoDemo-2008-08-11.png" width="650" border="0" alt="Dojo and ZF integration at work" /></a>

<p>
    What you're looking at is a form generated using Zend_Form (or, in this
    case, Zend_Dojo_Form). It contains four subforms, each represented by a tab.
    (I also created an additional decorator that creates the fifth tab.) The
    form elements are all Dijits, presenting a common look-and-feel, and have a
    variety of validators and features themselves.
</p>

<p>
    The fifth tab shows off some fun features of Dojo: the ContentPane actually
    has no content, but was defined with an 'href' attribute pointing to a
    resource on the server. On this particular page, I define a dojo Grid that
    consumes an ItemFileReadStore -- a fancy word for a remotable dojo.data
    resource. I used Zend_Dojo_Data to provide that, and the result is that,
    when you click on the tab, the following loads dynamically:
</p>

<a href="/uploads/gridDemo-2008-08-11.png"><img src="http://weierophinney.net/uploads/gridDemo-2008-08-11.png" width="650" alt="Dojo and ZF playing nice and creating grids" border="0" /></a>

<p>
    This stuff is ridiculously easy to pull off and create now -- which means I
    no longer have any excuse for crappy looking forms or not adding ajax
    features to apps. 
</p>

<p>
    In experimenting with Dojo, I also discovered its build tools. Once your
    code is developed, it's a trivial task to tally up all your dojo.require
    statements, throw them in a build profile, and create a layer for use with
    your application. Doing so has tremendous performance and bandwidth
    benefits, and can mean the difference between a good application and a great
    one, in terms of user experience. I'll blog more on this later, but I'm
    excited that, because we're shipping a full source build of Dojo, this
    support will be delivered to ZF users out of the box.
</p>

<h3>Unit testing infrastructure</h3>

<p>
    I've also <a href="http://weierophinney.net/matthew/archives/182-Testing-Zend-Framework-MVC-Applications.html">blogged about this before</a>. 
    I've been using the <code>Zend_Test_PHPUNit_ControllerTestCase</code> on
    some personal projects as well as projects related to application examples
    I'm working on for some upcoming presentations.
</p>

<p>
    There's nothing quite like having the assurance that your entire application
    -- not just your models -- works according to the expectations you've worked
    up, and that the document structure adheres to the design. This is going to
    become a must-have component for serious ZF developers.
</p>

<h3>Captcha Support</h3>

<p>
    <a href="http://en.wikipedia.org/wiki/Captcha">Captchas</a> have become an 
    essential tool in the anti-spam arsenal of most sites. While tools such as
    <a href="http://akismet.com/">Akismet</a> are fantastic, it's even better
    not to even need to send data to Akismet to process in the first place.
</p>

<p>
    <a href="http://php100.wordpress.com/">Stas</a> worked up a nice design for
    captchas as we were finishing 1.5.0, but we didn't have time to complete it
    before shipping the release. This turned out to be fortuitous, as we have
    received community contributions of <a href="http://framework.zend.com/manual/en/zend.text.html#zend.text.figlet">Figlet</a>
    and <a href="http://framework.zend.com/manual/en/zend.service.recaptcha">ReCaptcha</a> 
    support since then.  <a href="http://framework.zend.com/manual/en/zend.captcha">Zend_Captcha</a> 
    provides adapters for each of these, as well as for a GD-based image
    captcha. You can now pick and choose which works best for your site or
    altruistic leanings; using them in your forms is as simple as creating a
    Zend_Form element.
</p>

<h3>SOAP Support</h3>

<p>
    <a href="http://framework.zend.com/manual/en/zend.soap.html">Zend_Soap</a> 
    has been languishing in the incubator since before the 1.0 release. The WSDL
    autogeneration was mostly untested, and for several use cases, completely
    broken. Additionally, I had created but never completed testing and
    documenting Zend_Soap_Server, a thin wrapper on top of PHP's own SoapServer
    class.
</p>

<p>
    Alex took Zend_Soap on as a project for 1.6.0, and has done tremendous
    things with it. He has even created a <a href="http://framework.zend.com/wiki/display/ZFDEV/Zend_Soap_Client+compatibility+matrix">compatibility matrix</a> 
    that users can update to show how WSDL autodiscovery works with various SOAP
    clients -- which will help us improve it in the future.
</p>

<h3>Form Improvements</h3>

<p>
    There have been many feature requests and bugs filed against Zend_Form since
    its debut in 1.5.0, and I tried to address the most requested and critical
    of these for the release. There are several improvements that should make
    using Zend_Form easier and more flexible:
</p>

<ul>
    <li>
        Ability to set custom element or form errors. You can now specify the
        specific error message(s) to use if an element or form does not
        validate, as well as mark it as invalid. This is useful in particular
        when validating authentication; when authentication fails, you can mark
        the form invalid and specify a custom error message.
    </li>

    <li>
        Lazy-loading of filters, validators, and decorators. Previously, these
        were instantiated as soon as they were added to the element or form. Now
        the item metadata is simply stored; only when the item is retrieved is
        it instantiated. This offers some performance improvements in a number
        of areas: if you're not validating the form, the validators are not
        loaded; if you're not rendering the form, the decorators are not loaded.
        (One note: filters are almost invariably loaded, as they are used each
        time <code>getValue()</code> is called.)
        <br /><br />
        Another benefit of this change is that you can register plugin prefix
        paths any time before the plugin is actually retrieved. This should lead
        to less confusion and issues about event sequence, and provides
        additional flexibility to the component.
    </li>

    <li>
        Better array notation support. Several patches were provided by
        contributors, and I also re-worked the objects to do better name and id
        generation; the net result is seamless array notation for sub forms.
    </li>
</ul>

<h3>FireBug Support</h3>

<p>
    The author of <a href="http://www.firephp.org/">FirePHP</a>, Christoph Dorn, has committed a new component to ZF,
    <a
        href="http://framework.zend.com/manual/en/zend.wildfire.html">Zend_Wildfire</a>.
    <a href="http://www.wildfirehq.org/">Wildfire</a> is a project for standardizing communication
    channels between components; FirePHP is one implementation that communicates
    with the <a href="http://getfirebug.com/">FireBug</a> console for Firefox.
    The Zend_Wildfire component consists of a log writer,
    Zend_Log_Writer_Firebug, and a profiler, Zend_Db_Profiler_Firebug, which
    logs DB profiling information for use in FireBug. Together, these allow you
    to do more informed debugging in your browser, and can complement PHP
    debuggers such as XDebug and the Zend Debugger.
</p>

<h3>Pagination Support</h3>

<p>
    Raise your hand if you've ever needed to provide paginated result sets for
    your data -- good, I see a ton of virtual hands waving out there.
    Pagination is something that needs to be done frequently, usually needs to
    be tailored to your data and site, and is almost invariably a pain every
    single time. <a href="http://framework.zend.com/manual/en/zend.paginator.html">Zend_Paginator</a>
    aims to make it simple.
</p>

<p>
    Any iterable data set can be consumed by Zend_Paginator. You then need only
    specify what the current page number is and how many results per page to
    display -- it works out the calculations, and, when used in conjunction with
    the <code>paginationControl()</code> helper, will even provide your pager
    for you. No more writing the algorithms by hand.
</p>

<h3>And then there was...</h3>

<p>
    There are too many improvements and features really to count at this stage.
    We had originally intended this release to be smaller and offer fewer
    features, but as the time for release came closer, we discovered we had
    another monster on our hands. There is much to be excited about with this
    release -- and still much, much more in the wings for future releases.
</p>

<p>
    We are planning on tightening our release cycle so that we can offer more
    frequent and less intimidating releases in the future. Combine this with
    improvements we've been making to the proposal process, and you should be
    seeing more great things coming from the community -- more frequently!
</p>

<p>
    A huge thank you to all contributors, the Dojo team for answering my many
    questions, and my family for putting up with my long hours the last couple
    months. Happy developing!
</p>
EOT;
$entry->setExtended($extended);

return $entry;