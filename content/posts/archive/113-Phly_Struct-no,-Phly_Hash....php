<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('113-Phly_Struct-no,-Phly_Hash...');
$entry->setTitle('Phly_Struct? no, Phly_Hash...');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1148332080);
$entry->setUpdated(1148388987);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    After some discussion with <a href="http://paul-m-jones.com/blog/">Paul</a>
    and <a href="http://mikenaberezny.com/">Mike</a>, I was convinced that
    'Struct' was a bad name for Phly_Struct; structs are rarely if ever
    iterable, and one key feature of Phly_Struct is its iterable nature.
</p>
<p>
    The question is: what to name it? Associative arrays go by a variety of
    names in different languages. In Perl, they're 'hashes'; Ruby and
    Javascript, 'collections'; Python, 'dictionaries'. I ruled out
    'Phly_Dictionary' immediately, as (a) I don't want it to be confused with
    online dictionaries, and (b), it's too long. The term 'Collection' also
    feels too long (although I write things like
    'Cgiapp2_ErrorException_Observer_Interface', so I don't know why length
    should be such an issue), as well as unfamiliar to many PHP developers. Hash
    can imply cryptographic algorithms, but, overall, is short and used often
    enough in PHP circles that it makes sense to me.
</p>
<p>
    So, I've renamed Phly_Struct to <a href="http://weierophinney.net/phly/index.php?package=Phly_Hash">Phly_Hash</a>,
    and updated Phly_Config to use the new package as its dependency. In
    addition, I've had it implement Countable, so you can do things like:
</p>
<div class="example"><pre><code lang="php">
$idxCount = count($struct);
</code></pre></div>
<p>
    Go to the <a href="http://weierophinney.net/phly/">channel page</a> for
    instructions on adding Phly to your PEAR channels list, and grab the new
    package with <tt>pear install -a phly/Phly_Hash</tt>, or <tt>pear upgrade -a
    phly/Phly_Config</tt>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;