<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('19-use-autouse-...-or-not');
$entry->setTitle('use autouse ... or not');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074994577);
$entry->setUpdated(1095701361);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'perl',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    Due to my cursory reading in the <em>Perl Cookbook, 2nd Edition</em>,
    earlier this week, I've been investigating the <tt>use autouse</tt> pragma,
    to see if it will indeed solve my issue of wanting to use different modules
    based on the current situation. Unfortunately, I cannot find any
    documentation on it in <tt>perldoc</tt>.
</p>
<p>
    I remember seeing something about wrapping this stuff into a <tt>BEGIN</tt>
    block, but that would require knowing certain information immediately, and I
    might need the code to work through some steps before getting there.
</p>
<p>
    Fortunately, <a href="http://www.perlmonks.org/index.pl?node_id=323606">this
        node</a> just appeared on Perl Monks today, and I got to see other ways
    of doing it:
</p>
<ul>
    <li>The <tt>if</tt> module lets you do something like <tt>use if $type eq
        'x', "Some::Module";</tt> However, $type must be known at compile time
        (i.e., it's based on system info or on @ARGV); this probably wouldn't
        work in a web-based application.
    </li>
    <li>Use <tt>require</tt> and <tt>import</tt> instead: <tt>if $type wq 'ex')
        { require Some::Module; Some::Module->import if
        Some::Module->can("import"); }</tt> If your module doesn't export
        anything, you can even omit the call to <tt>import</tt>.
    </li>
    <li>Use an <tt>eval</tt>: <tt>if ($type eq 'x') { eval "use Some::Module";
        }</tt> This gets around the <tt>import</tt> problem, but could possibly
        run into other compile time issues.
    </li>
</ul>
<p>
    So, basically, I already had the tools to do the job; just needed to examine
    the problem more.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;