<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('142-File_Fortune-refactored');
$entry->setTitle('File_Fortune refactored');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1183670760);
$entry->setUpdated(1184070892);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'pear',
));

$body =<<<'EOT'
<p>
    Over the past few evenings, I've refactored <a href="http://pear.php.net/trackback/trackback.php?id=File_Fortune">File_Fortune</a>
    to have it implement Iterator, Countable, and ArrayAccess -- basically
    allowing it to act like an array for most intents and purposes. As a result,
    I've eliminated the need for the <kbd>File_Fortune_Writer</kbd> package, and
    greatly simplified the usage.
</p>

<p>
    (Note: sure, File_Fortune may not be that big of a deal, but over 1000
    downloads in the past two years indicates <em>somebody</em> is using it.
    Plus, it powers the random quotes on the family website. :-) )
</p>

<p>
    As some examples:
</p>
<div class="example"><pre><code lang="php">
require_once 'File/Fortune.php';

// Initialize and point it to a directory of fortunes
$fortunes = new File_Fortune('/path/to/fortunedir');

// Retrieve a random fortune 
// (works with either a directory or a single fortune file)
echo $fortunes-&gt;getRandom();

// Set to a specific fortune file:
$fortunes-&gt;setFile('myfortunes');

// Loop through and print all fortunes
foreach ($fortunes as $fortune) {
    echo str_repeat('-', 72), \&quot;\n\&quot;, $fortune, \&quot;\n\n\&quot;;
}

// Hmmm.. let's change one:
$fortunes[7] = \&quot;I never really liked that fortune anyways.\&quot;;

// No need to explicitly save, as it's done during __destruct(), 
// but if you really want to:
$fortunes-&gt;save();

// Let's add a new fortune:
$fortunes-&gt;add('This is a shiny new fortune!');

// and now we'll verify it exists:
$index = count($fortunes) - 1;
echo $fortunes[$index];
</code></pre></div>

<p>
    All-in-all, it's a much better interface. Lesson learned: when porting code
    from other languages, it pays to take some time and determine if there might
    be a better API in your own.
</p>
<p>
    In upcoming releases, I hope to modify the backend to use PHP's Streams API
    instead of direct file access, and also to allow providing a list of fortune
    files explicitly. After that, I should be ready for the initial stable
    release.
</p>

<p><b>Update (2007-07-10): fixed parse error in examples</b></p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;