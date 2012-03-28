<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('61-PHP,-Cgiapp,-and-extensibility');
$entry->setTitle('PHP, Cgiapp, and extensibility');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1112496070);
$entry->setUpdated(1112500873);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    At work this week, Rob was doing some monitoring of our bandwidth usage. We
    have SNMP on each of our servers now, and he uses MRTG to create bandwidth
    usage graphs that are updated every five minutes or so. He's been monitoring
    since late last year.
</p>
<p>
    Before January, we had two systems going. The first, legacy, system hosted
    the majority of the content from garden.org, and was done using Tango 2000,
    a web application server that ran on top of IIS and Windows NT 4. I say
    'ran', because Tango 2000 was the last version to ship; the company that
    made it stopped supporting it a year later. This meant we could not upgrade
    our server's OS to Windows 2000 or 2003, nor could we switch to a more
    secure web server, etc. It was a time bomb waiting to happen.
</p>
<p>
    The second system is a basic LAMP system -- Linux + Apache + MySQL + PHP.
    Rob began migrating applications to it shortly after he started at NGA 3
    years ago, one application at a time. Mostly, new applications were placed
    on it, though in May 2003, he and the other programmer who was there at the
    time started migrating old applications to the techology. Part of the reason
    I was hired was to continue this migration.
</p>
<p>
    The migration was time consuming, and plenty of other projects often got in
    the way. However, starting last July, we made a big push to get it all
    ported over -- before the old WinNT server fails on us. In January, we were
    able to rollout the new garden.org, which runs on this new technology.
</p>
<p>
    A big reason we were able to finish is because of Cgiapp. I originally
    ported it to PHP last year around this time, and knew that while I wanted to
    develop new applications using it, I wasn't so sure I could sell Rob on it.
</p>
<p>
    Amazingly, it didn't take much to convince him. We had already started using
    Smarty for templates just before this, and were also using OOP in new
    development. Cgiapp just helped unify these technologies and to provide a
    nice, standard framework with which to program.
</p>
<p>
    This last can not be emphasized enough. We started developing all
    applications in three places: an API for data access, a Cgiapp-based
    application, and our templates. Either one of us could pick up development
    of an application from the other without having to spend a day or two
    familiarizing ourselves with the idiosyncracies of what the other had
    decided was the programming paradigm of the day. Sure, we still have our own
    programming styles, but the framework makes it easy to debug or extend each
    others programs painlessly.
</p>
<p>
    Now, back to the bandwidth reports: Rob has noticed that our bandwidth usage
    has been growing steadily on the new server since we switched garden.org
    over -- a 45 degree line. At one point this week, our outgoing bandwidth was
    almost 3 T1s -- and we were having no performance issues whatsoever. This
    simply would not have been possible on the old system -- nor without Cgiapp.
    We've managed to produce both a hardware architecture and a programming
    framework that has proved immensely scalable -- which will in turn save the
    organization money.
</p>
<p>
    I love open source! How else can you create such high-performing software
    without paying through the nose for it?
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;