<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('195-Zend-Framework-1.7.0-Released');
$entry->setTitle('Zend Framework 1.7.0 Released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1226950154);
$entry->setUpdated(1227107231);
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
    Today, we released <a href="http://framework.zend.com/download/latest">Zend Framework 1.7.0</a>. 
    This release features <a href="http://framework.zend.com/manual/en/zend.amf.html">AMF support</a>, 
    <a href="http://framework.zend.com/manual/en/zendx.jquery.html">JQuery support</a>,
    and <a href="http://framework.zend.com/manual/en/zend.service.twitter.html">Twitter support</a>,
    among numerous other offerings.
</p>

<p>
    For this particular release, we tried very hard to leverage the community.
    The majority of new features present in 1.7.0 are from community proposals,
    or were primarily driven by community contributors. For me, this represents
    a milestone: ZF is now at a stage where fewer and fewer core components are
    necessary, and the community is able to build off it and add extra value to
    the project. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    On that note, this release also marks the first release containing the
    Extras library -- a repository of community driven components that will not
    be officially supported by Zend, but which must also pass ZF's strict
    guidelines for submission (&gt; 80% test coverage, fully documented, and
    reviewed by the internal team). We hope that this repository continues to
    expand and show off the diverse interests of our contributors.
</p>
    
<p>
    In particular, I'm quite proud of the jQuery support. From the moment we
    first announced our partnership with <a href="http://dojotoolkit.org">Dojo</a>, 
    we messaged that we while we would officially support Dojo in the framework,
    we would also allow community contributions for integration with other
    frameworks. <a href="http://www.whitewashing.de/blog/">Benjamin Eberlei</a>
    drove this component from proposal to implementation, and communicated often
    with me to ensure that it would provide a story consistent with our Dojo
    integration. I think jQuery users will be pleased with the results.
</p>

<p>
    Besides providing a wealth of new components for the release, the community
    also stepped up to help resolve bugs in the framework. We held a general bug
    hunt week the week of 3 November 2008, in which we resolved approximately
    100 issues. Additionally, <a href="http://www.phpgg.nl">phpGG</a> and <a
        href="http://www.phpbelgium.be">PHPBelgium</a> banded together to start
    the <a href="http://bughuntday.org/">Bug Hunt Day</a> initiative, and held
    their first event on 8 November 2008 -- dedicated to fixing Zend Framework
    bugs. While we had but a dozen issues closed during that event, I
    anticipate that such initiatives in the future will bring more people to the
    project, and help increase the overall quality of all projects they target.
    My hearty thanks to all participants involved!
</p>

<p>
    My primary involvement in this release was coordinating the bug hunts, as
    well as working on performance benchmarking and profiling. I'll blog on this
    topic more in the future, but I found some areas where ZF can be tuned very
    efficiently and concisely to bring some significant performance gains to
    your applications. I have begun writing a 
    <a href="http://framework.zend.com/manual/en/performance.html">Performance Guide</a> 
    appendix to the manual, and you can look for updates to that in upcoming
    releases.
</p>

<p>
    So, <a href="http://framework.zend.com/download/latest">grab 1.7.0 today</a>, 
    and start enjoying the new features!
</p>
EOT;
$entry->setExtended($extended);

return $entry;