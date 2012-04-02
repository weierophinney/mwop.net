<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('51-Sign-of-a-Geek');
$entry->setTitle('Sign of a Geek');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1100725479);
$entry->setUpdated(1100725542);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'perl',
  1 => 'programming',
  2 => 'personal',
));

$body =<<<'EOT'
<p>
    It's now been confirmed: I'm a geek. 
</p>
<p>
    Okay, so that probably comes as no shocker to those of you who know me, but
    it's the little things that make me realize it myself.
</p>
<p>
    I've been frequenting <a href="http://www.perlmonks.org">Perl Monks</a> for
    a couple of years now, mainly to garner ideas and code to help me with my
    personal or work projects. I rarely post comments, and I've only once
    submitted a question to the site. However, I <strong>do</strong> frequent
    the site regularly, and the few comments I've put in -- generally regarding
    usage of <a href="http::/search.cpan.org/search?query=CGI%3A%3AApplication">CGI::Application</a>
    -- have been typically well-moderated.
</p>
<p>
    Well, yesterday I <a href="http://www.perlmonks.org/?node_id=408255">made a comment</a> to a user <a href="http://www.perlmonks.org/?node_id=408231">asking about editors to
        use with perl</a>. I was incensed by a remark he made about <a href="http://www.vim.org">VIM</a> not having the features he needed.
    Now, as I said in my comment, I've used VIM on a daily basis for over two
    years, and I'm <em>still</em> discovering new features -- and I've used all
    of the features he was looking for.
</p>
<p>
    This is where I discovered I'm a geek: my comment made it into the <a href="http://www.perlmonks.org/?node=Best%20Nodes">Daily
    Best</a> for today, peaking around number 5. The fact that that made my day
    indicates to me that I <em>must</em> be a geek.
</p>
<p>
    Oh -- and VIM rules!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;