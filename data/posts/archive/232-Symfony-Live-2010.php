<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('232-Symfony-Live-2010');
$entry->setTitle('Symfony Live 2010');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1266424783);
$entry->setUpdated(1266796380);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'sflive2010',
  2 => 'symfony',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    This week, I've been attending <a
        href="http://www.symfony-live.com/">Symfony Live</a> in Paris, speaking
    on integrating Zend Framework with Symfony. The experience has been quite
    rewarding, and certainly eye-opening for many.
</p>

<p>
    To be honest, I was a little worried about the conference -- many see
    Symfony and ZF as being in competition, and that there would be no
    cross-pollination. I'm hoping that between <a
        href="http://fabien.potencier.org/">Fabien</a>, <a
        href="http://www.leftontheweb.com/">Stefan</a>, and myself, we helped
    dispel that myth this week.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    The fact of the matter is that no single project can be fully comprehensive,
    and do everything perfectly. In my examinations of different frameworks, PHP
    and otherwise, the places where they most differ and which generates the
    most loyalty amongst users are the MVC approaches and tooling support. In
    good frameworks, this is just a portion of the code, and the remainder is in
    support libraries or plugins that extend that functionality.
</p>

<p>
    This is true of both Symfony and Zend Framework. Symfony's development team
    has chosen to focus on a very specific core of functionality related to the
    MVC approach, which makes their maintenance job easier, and leads to a
    stable product. Zend Framework's MVC implementation is offered as a group of
    separate components, with components such as Zend_Application and Zend_Tool
    helping to bring cohesion and structure to them.
</p>

<p>
    What this means is that once you've developed the basic infrastructure of
    your application, the scaffolding, you're now left with decisions about how
    to implement the actual functionality of the application itself. The problem
    as I see it is: how do you do that development? Many developers are myopic
    and will not look beyond the framework they have chosen for for development.
    This can lead to multiple implementations of the same code, and often leads
    to incomplete implementations as well.
</p>

<p>
    My feeling is that whenever you find yourself about to write new code, look
    to see if somebody else has written the code already. Anybody -- don't limit
    yourself to your framework of choice. If I want to do serious HTML sniffing,
    validation, and cleanup, I go to <a
        href="http://htmlpurifier.org/">HTMLPurifier</a>; if I want a workflow
    component, I check out <a
        href="http://www.ezcomponents.org/docs/api/latest/introduction_Workflow.html">eZ
        Components Workflow</a>; I always check <a
        href="http://pear.php.net/">PEAR</a>.
</p>

<p>
    This week, I tried to spread this message within the <a
        href="http://symfony-project.org">Symfony</a> community, showing them
    how easy it is to integrate ZF components within Symfony projects. The
    integration itself is simple: instantiate the Zend autoloader, and start
    using ZF classes. This same technique can be used to load PEAR, or
    eZComponents, or Doctrine 2, etc. The trick is getting out of the "Not
    Invented Here" syndrome, letting go of your ego, and using <em>other</em>
    people's code.
</p>

<p>
    (Yes, I know we have code in ZF duplicating functionality in other
    libraries; in most cases, we try and offer at least a new approach to the
    problem -- but we could do better.)
</p>

<p>
    Fabien also made an interesting announcement. During a Q&amp;A session with
    the Symfony core team, he said that Symfony 2 will not write re-invent the
    wheel when it doesn't need to -- and announced that Symfony 2 will be using
    <code>Zend_Log</code> and <code>Zend_Cache</code> instead of rewriting the
    current Symfony components. I find this admirable -- and it's something I'm
    hoping to do in a few places with Zend Framework 2.0 as well, as I know
    there are features and code that others have, quite simply, written better.
</p>

<p>
    One last note in this ramble: With the various "2.0" versions of frameworks,
    most projects are learning from both mistakes made as well as from the usage
    patters of the developers adopting them. One of those lessons, to my mind,
    is that no one framework can do it all well and by themselves. I fully
    expect to see the next generation of frameworks making it trivial to pull
    features from other frameworks and libraries in order to fill out
    functionality.
</p>
EOT;
$entry->setExtended($extended);

return $entry;