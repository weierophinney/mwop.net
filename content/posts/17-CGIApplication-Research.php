<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('17-CGIApplication-Research');
$entry->setTitle('CGI::Application Research');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074913142);
$entry->setUpdated(1095701174);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'perl',
  2 => 'personal',
));

$body =<<<'EOT'
<p>
    I've been wanting to redevelop my home website for some time using
    CGI::Application. The last time I rewrote it from PHP to perl, I developed
    something that was basically a subset of the things CGI::App does, and those
    things weren't done nearly as well.
</p>
<p>
    The problem I've been running into has to do with having sidebar content,
    and wanting to run basically a variety of applications. I want to have a
    WikiWikiWeb, a photo gallery, some mail forms, and an article database/blog;
    CGI::App-based modules for each of these all exist. But I want them all to
    utilize the same sidebar content, as well -- and that sidebar content may
    vary based on the user.
</p>
<p>
    My interest got sparked by <a href="http://www.perlmonks.org/index.pl?node_id=320946">this node</a> on
    <a href="http://www.perlmonks.org">Perl Monks</a>. The author tells of an
    acquaintance who goes by the rule that a CGI::App should have 10-12 states
    at most; more than that, and you need to either break it apart or rethink
    your design. And all CGI::Apps inherit from a common superclass, so that
    they share the same DB connections, templates, etc.
</p>
<p>
    So, I've been investigating this problem. <a href="http://www.perlmonks.org/index.pl?node_id=229260">One node on PM</a>
    notes that his ISP uses CGI::App with hundreds of run modes spread across
    many applications; they created a module for session management and access
    control that calls <tt>use base CGI::Application</tt>; each aplication then
    calls <tt>use base Control</tt>, and they all automatically have that same
    session management and access, as well as CGI::Application.
</p>
<p>
    <a href="http://www.perlmonks.org/index.pl?node_id=94879">Another node</a>
    mentions the same thing, but gives a little more detail. That author writes
    a module per application, each inheriting from a super class:
    UserManager.pm, Survey.pm, RSS.pm, Search.pm, etc. You create an API for
    that super class, and each CGI::App utilizes that API to do its work.
</p>
<p>
    This also seems to be the idea behind <a href="http://cheesepizza.venzia.com">CheesePizza</a>, a CGI::App-based
    framework for building applications. (All pizzas start out as cheese pizzas;
    you simply add ingredients.) The problem with that, though, is that I have
    to learn another framework on top of CGI::App, instead of intuiting my own.
</p>
<p>
    But how do I write the superclass? Going back to the original node that
    sparked my interest, I found a <a href="http://www.perlmonks.org/index.pl?node_id=321064">later reply</a>
    that described how you do this. The big key is that you override the
    <tt>print</tt> method -- this allows you to customize the output, and from
    here you could call functions that create your sidebar blocks, and output
    the content of the CGI::App you just called in a main content area of your
    template.
</p>
<p>
    Grist for the mill...
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;