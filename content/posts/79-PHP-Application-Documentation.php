<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('79-PHP-Application-Documentation');
$entry->setTitle('PHP Application Documentation');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1118804877);
$entry->setUpdated(1118871113);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p> <a href="http://www.paul-m-jones.com/">Paul Jones</a> has written <a href="http://www.paul-m-jones.com/blog/?p=153">an interesting piece about documentation in the PEAR project</a>, in which he argues very convincingly for using wikis for end user documentation.</p>
<p>
    I actually think that last point bears repeating: <em>using wikis for end
        user documentation</em>. I talked to Paul about this issue at
    php|Tropics, and both of us use phpDocumentor quite religiously. However,
    API documentation is very different from end user documentation. And the
    issue with documentation at the PEAR project has to do with the fact that
    there are many projects with little or no end user documentation -- which
    often makes it difficult for a developer to determine how a module might be
    used.
</p>
<p>
    The often-cited barrier for this is that end user documentation on the PEAR
    website must be done in DocBook format.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    For the record, I <strong>hate</strong> maintaining DocBook format. I
    created the tutorials for Cgiapp using it, and I dread having to go back
    into them to update or add new sections.
</p>
<p>
    Why? Well, for starters:
</p>
<ol>
     <li>I don't use XML on a daily basis. If I need to deal with XML, I
     typically create a template and have a script fill it. Or I use a parser.
     But I don't write it by hand.</li>
     <li>DocBook doesn't use the same tagset as HTML. This means that I have to
     try and remember different tags, and which work in which arena.</li>
     <li>Related to (2) is that because the actual tags available can vary based
     on the DTD, VIM doesn't have keystroke macros to create the begin/close
     tags. This is a feature I use in editing HTML daily, and which speeds up my
     writing time. So, writing DocBook is slower than writing HTML (or plain
     text) by a significant factor. (Yes, I could write VIM macros for often
     used tags, but then I'd need to learn more about VIM scripting, and who has
     the time?)</li>
     <li>I already know and use HTML on a daily basis. I use plain text on a
     daily basis (did I mention I use VIM?). I'm comfortable in these
     environments. Why would I use anything else?</li>
</ol>
<p>
    But I think the point that is often overlooked is that <em>PHP was written
    to create web pages</em>. Let that sink in for a moment. Why would a PHP
    project encourage writing documentation in anything other than the language
    of the web, HTML? Indeed, why would it <em>discourage</em> writing web-ready
    documentation?
</p>
<p>
    As Paul noted, while wikis may not be great for all documentation purposes,
    they're more than adequate for <em>most</em>. In most projects, you're not
    going to need tables to document the project; several levels of headings,
    paragraphs, and some list elements will do the trick. Wikis can do all of
    these things. And they allow these things to be done easily, and for the
    results to be instantly available (rather than on a once-daily basis, as is
    the case for the PEAR web documentation, which must be compiled from
    DocBook).
</p>
<p>
    My suggestion? Since DocBook can be exported to almost anything, export the
    PEAR documentation to a wiki format, and then use wikis for all but the most
    complex documentation. Do the complex docs in HTML, or, if you
    <em>really</em> feel the need for an output-agnostic format, use DocBook
    then. (My guess is that if the above were implemented, we wouldn't see much
    DocBook after more than a year.)
<p>
    Maybe by reducing the barrier to creating usable end-user documentation,
    we'll start seeing a proliferation of documentation on PEAR to augment the
    great code that's been appearing there.
</p>
EOT;
$entry->setExtended($extended);

return $entry;