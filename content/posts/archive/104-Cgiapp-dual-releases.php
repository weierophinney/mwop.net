<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('104-Cgiapp-dual-releases');
$entry->setTitle('Cgiapp dual releases');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1140390167);
$entry->setUpdated(1140391855);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Today, I have released two versions of Cgiapp into the wild, Cgiapp 1.8.0
    and Cgiapp2 2.0.0rc1.
</p>
<p>
    Cgiapp 1.8.0 is a performance release. I did a complete code audit of the
    class, and did a number of changes to improve performance and fix some
    previously erratic behaviours. Additionally, I tested under both PHP4 and
    PHP5 to make sure that behaviour is the same in both environments.
</p>
<p>
    However, Cgiapp 1.8.0 markes the last feature release of Cgiapp. I am
    deprecating the branch in favor of Cgiapp2.
</p>
<p>
    Cgiapp2 is a PHP5-only version of Cgiapp. Some of the changes:
</p>
<ul>
    <li>Cgiapp2 is an abstract class, with the abstract method setup(). Now it
    is truly non-instantiable!</li>
    <li>Cgiapp2 makes extensive use of visibility operators. Key methods have
    been marked final, some methods are now protected, others static. See <a href="http://cgiapp.sourceforge.net/index.php/view/Changelog2">the changelog</a> for more information.</li>
    <li>Cgiapp2 is now E_STRICT compliant.</li>
    <li>Cgiapp2 implements the CGI::Application 4.x series callback hook system.
    This is basically an observer pattern, allowing developers to register
    callbacks that execute at different locations in the runtime.</li>
    <li>Cgiapp2 adds some extensive error and exception handling classes,
    including observable errors and exceptions.</li>
    <li>I created a template interface. If implemented, a template engine can be
    plugged into the architecture at will -- at the superclass, application
    class, and instance script level, allowing developers to mix-and-match
    template engines or choose whichever matches their taste, <em>without</em>
    having to rewrite application code. Three template plugins are included:
    <ul>
        <li><a href="http://smarty.php.net/">Smarty</a></li>
        <li><a href="http://phpsavant.com/yawiki/">Savant2</a></li>
        <li><a href="http://phpsavant.com/yawiki/index.php?area=Savant3&page=HomePage">Savant3</a></li>
    </ul>
    </li>
</ul>
<p>
    <a href="http://sourceforge.net/project/showfiles.php?group_id=125419&package_id=137071">Cgiapp</a> and <a href="http://sourceforge.net/project/showfiles.php?group_id=125419&package_id=180286">Cgiapp2</a> are available at
    Sourceforge.
<p>
    Keep reading for more information on the evolution of Cgiapp2.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I've been loathe to make Cgiapp (1) PHP5-only, and (2) into a version 2
    style PEAR package (e.g., Cgiapp2 versus Cgiapp). However, I realized
    recently that both were necessary.
</p>
<p>
    I've been doing a lot of reading the past 6 months on design patterns, and
    also exposed to a bunch of advanced PHP5 code since my employment at Zend.
    In developing Cgiapp2, I decided to put both to use so I could help advance
    my coding skills.
</p>
<p>
    At this point, I don't know how I ever thought I could successfully port
    Cgiapp without PHP5. Having the ability to define abstract classes, static
    properties and methods, and marking methods as final is truly useful and
    makes the class much more robust.
<p>
    PHP5 is truly a great advance for PHP4. I had been somewhat ambivalent about
    it before; I liked the ability to pass objects without using the reference
    notation (e.g., '=&amp;'), and had used SimpleXML a little, but overall
    didn't see much use to the new features. With my introduction to design
    patterns, I started seeing more uses for them, as some design patterns are
    difficult, if not impossible, to implement without them. The visibility
    operators are truly useful. Static properties and methods are also
    incredibly useful.  Exceptions completely change the face of error handling
    for PHP. I've even found uses for the Reflection API, something I never
    thought I'd do.
</p>
<p>
    If you're not using PHP5 yet, you're doing yourself a disservice. Upgrade
    today, and start coding projects that make use of the new OOP model.
</p>
<p>
    Regarding the versioning numbers, I realized that if I'm going to have two
    stable versions of Cgiapp around, having them both named Cgiapp would make
    it difficult for developers to migrate -- they wouldn't be able to have them
    easily accessible on the same machine at the same time. Once again, the PEAR
    group got it right, when it comes to backwards compatibility. Thus, Cgiapp
    2.0.0 was renamed to Cgiapp2 2.0.0. 
</p>
<p>
    Enjoy the new releases!
</p>
EOT;
$entry->setExtended($extended);

return $entry;