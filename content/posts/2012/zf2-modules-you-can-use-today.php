<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('zf2-modules-you-can-use-today');
$entry->setTitle('ZF2 Modules You Can Use Today');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1328718707);
$entry->setUpdated(1328718707);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
  2 => 'zf2',
));

$body =<<<'EOT'
<p>
    One key new architectural feature of Zend Framework 2 is its new module
    infrastructure. The basic idea behind modules is to allow developers to both
    create and consume re-usable application functionality -- anything from
    packaging common assets such as CSS and JavaScript to providing MVC
    application classes.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    As an example, for my own site, I've created: a "Contact" module for
    rendering and processing contact forms; a "SkeletonCSS" module for dropping
    in <a href="http://getskeleton.com">Skeleton</a> into my sites; a
    "CommonResource" module with a very, very basic data mapper implementation,
    and a "Blog" module that consumes it to deliver the very blog you're reading
    now.
</p>

<p>
    But the <em>real</em> goal of the module infrastructure is for developers to
    <em>share</em> their modules, so that other developers don't need to develop
    that very same functionality for their own site. And to my delight, that's
    already happening!
</p>

<p>
    So, here's a list of some of the ZF2 modules I've found in the wild.
</p>

<ul>
    <li><a href="http://bit.ly/yVBXkw">SkeletonCss</a>. I mentioned this one
        already, but it's a version of SkeletonCss, an adaptive response CSS/JS
        framework, packaged for easy consumption as a ZF2 module. The git URI is
        "git://mwop.net/SkeletonCss.git".</li>

    <li><a href="https://github.com/doctrine/DoctrineModule">DoctrineModule</a>.
        One of the most frequently asked questions I get is, "Will ZF2 use
        Doctrine?" My answer has been that we'll provide a bridge -- but the cool
        thing is that the Doctrine project has already decided to do it.
        Spear-headed by an enthusiastic ZF2 contributor/collaborator, <a
            href="http://twitter.com/SpiffyJr">Kyle Spraggs</a>, this module
        provides the base functionality for integrating Doctrine 2 into your ZF2
        site (primarily access to the base/common functionality). Two other
        modules provide additional functionality: <a
            href="https://github.com/doctrine/DoctrineORMModule">DoctrineORMModule</a>
        provides the ability to interact with the Doctrine 2 ORM, and <a
            href="https://github.com/doctrine/DoctrineMongoODMModule">DoctrineMongoODMModule</a>
        provides the ability to interact with and consume the Doctrine 2 Object
        Document Mapper for MongoDB.</li>

    <li><a href="https://github.com/EvanDotPro/EdpSuperluminal">EdpSuperluminal</a>.
        This module can be used to cache all classes you use in your application
        to a single include file -- giving you a performance boost.</li>

    <li><a href="https://github.com/ZF-Commons/ZfcUser">ZfcUser</a>. This module
        was begun by <a href="http://evan.pro">Evan Coury</a>, the lead
        developer behind ZF2's module system; its purpose is to provide a
        drop-in solution for registering and authenticating users. The module
        itself provides functionality consuming Zend\Db; however, several other
        modules are also offered to provide other persistence layers, including
        a <a href="https://github.com/ZF-Commons/ZfcUserDoctrineORM">ZfcUserDoctrineORM</a>
        module and a <a href="https://github.com/ZF-Commons/ZfcUserDoctrineORM">ZfcUserDoctrineMongoODM</a>
        module; more are planned. The "Zfc" namespace is used because Evan realized
        he and several others were working on similar solutions, and felt that
        collaboration would lead to a better solution than each would develop
        individually; this in turn led to the creation of a "Zend Framework
        Commons" organization on GitHub, with the goal of providing high-quality
        modules solving common application problems.</li>

    <li><a href="https://github.com/widmogrod/zf2-assetic-module">AsseticBundle</a>.
        This is a module providing integration with the excellent <a
            href="https://github.com/kriswallsmith/assetic">Assetic</a> asset
        management framework.</li>
            
    <li><a href="https://github.com/widmogrod/zf2-twitter-bootstrap-module">TwitterBootstrap</a>.
        Many developers are gravitating to the <a
            href="https://github.com/twitter/bootstrap">Twitter Bootstrap</a>
        project for CSS layouts. This module depends on the AsseticBundle
        already listed above, and provides both Twitter Bootstrap as well as
        integration with the current (ZF1) incarnation of Zend\Form.</li>
</ul>

<p>
    I've seen a number of others as well, and know of more on their way (as an
    example, a ZfcAcl module to complement the ZfcUser module). Writing modules
    is incredibly easy, and a great way to both learn ZF2 and collaborate and
    share with other developers.
</p>

<p>
    Where are <em>your</em> modules?
</p>
EOT;
$entry->setExtended($extended);

return $entry;