<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('74-PEAR-and-DocBook-documentation');
$entry->setTitle('PEAR and DocBook documentation');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1116779474);
$entry->setUpdated(1116787015);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Sean Coates has posted a rant and <a
        href="http://blog.phpdoc.info/comment.php?type=trackback&entry_id=14">a
        blog entry</a> regarding DocBook and the various proposals on PEAR
    related to using a wiki for package documentation.
</p>
<p>
    A little background is probably in order. Many PEAR developers feel that
    DocBook is needlessly difficult and provides a barrier to writing good
    documentation for PEAR projects; this is actually the most often-cited
    reason for lack of documentation for a PEAR package. Many actually create
    wikis that they then link to in a minimal DocBook tutorial as "full
    documentation".
</p>
<p>
    One proposed remedy is to create a PEAR wiki for each PEAR package. A
    scheduled process would then transform the wiki markup to DocBook, HTML,
    PDF, whatever.
</p>
<p>
    What Sean rants about is simply this: wiki markup is meant to be simple,
    and much code documentation would require specialized wiki markup.
    Additionally, DocBook is already meant to do the transformations required;
    it is a structured language that is meant to be processed into a variety of
    output formats.
</p>
<p>
    While I agree with Sean's ideas in essence, I still feel that DocBook is a
    real pain to work with. <a href="http://www.phpdoc.org">PhpDocumentor</a>
    offers some real convenience when documenting code: doc blocks can contain
    HTML, some simple inline elements like {@link} -- and they make documenting
    a snap. But I just fail to understand why, when providing tutorials, a
    switch to DocBook is necessary. Whenever I use it, I find that I have to
    retool a set of tutorials I have, or somebody else has, already written in
    order to get formatting correct, and that I have to switch my thinking
    altogether to accomodate a new set of rules and logic.
</p>
<p>
    Yes, DocBook is simply XML with a documented schema. However, I've never
    enjoyed XML. I find it too pedantic, I don't like having to escape out CDATA
    sequences in order to render HTML (and code, and XML, etc.), I don't like
    having to learn new DTDs for every project, and more. I feel for
    configuration, unless you have nested elements, there's no reason to use XML
    whatsoever. And when it comes to documentation, why use anything other than
    HTML? Since HTML is a subset of SGML (as is XML), there's no reason it can't
    be transformed to other formats itself -- and for the majority of PHP
    developers, HTML is a known, while XML/DocBook may or may not be.
</p>
<p>
    Whether or not DocBook is hard can be debated from here to eternity. The
    fact of the matter is that it is <em>perceived</em> as being hard to learn,
    and thus many PEAR developers are simply choosing not to bother. Why not
    give them a tool they can use <em>easily</em>? Maybe then the amount and
    quality of documentation on PEAR will improve.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;