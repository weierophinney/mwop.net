<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('239-php-invades-amsterdam-or-the-dutch-php-conference');
$entry->setTitle('PHP Invades Amsterdam; or, the Dutch PHP Conference');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1271735917);
$entry->setUpdated(1271770546);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'dpc10',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    For the third year running, I'm pleased to be speaking at the <a
        href="http://phpconference.nl/">Dutch PHP Conference</a>, held again in
    Amsterdam this coming 10-12 of June.
</p>

<p style="text-align:center">
    <a href="http://phpconference.nl/" title="2010 Dutch PHP Conference">
        <img src="/uploads/dpc10_speaker.jpg" />
    </a>
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    Once again, I'll be doing another <a href="http://framework.zend.com/">Zend
        Framework</a> workshop, but this year I'll be teaming up with <a
        href="http://akrabat.com/">Rob Allen</a>. Our focus this year will be on
    oft-used application patterns found in the ZF ecosystem, and how you can
    weave them together to create complex and robust applications.
</p>

<p>
    I'm also presenting two regular sessions. The first is a session on writing
    RESTful web services with Zend Framework. My goal is to show you a handful
    of techniques that should get you up and running, building RESTful
    applications that can respond to normal HTML requests as well as XML and
    JSON payloads with a minimum of effort.
</p>

<p>
    My second session will be a continuation of my "Models" talk, but this time
    looking at domain models from the perspective of NoSQL databases. "NoSQL"
    means "Not Only SQL", and refers to the new proliferation of data stores
    that focus on key/value storage and document storage. They offer some new
    paradigms to how we store and retrieve data, and also introduce some
    potentially more flexible, lightweight ways to map your domain objects to
    the data store.
</p>

<p>
    There will be a ton of great speakers at DPC again this year -- from
    stalwart PHP veterans such as Chris Shiflett and Sebastian Bergmann to core
    PHP developers like Scott MacVicar, Ilia Alshanetsky and Melanie Rhianna
    Lewis, and many, many more. It's not one to miss!
</p>

<p>
    Hoping to meet <em>you</em> in Amsterdam this June!
</p>
EOT;
$entry->setExtended($extended);

return $entry;
