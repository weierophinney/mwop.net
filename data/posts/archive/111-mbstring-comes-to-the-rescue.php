<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('111-mbstring-comes-to-the-rescue');
$entry->setTitle('mbstring comes to the rescue');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1147818300);
$entry->setUpdated(1147973133);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I've been working with SimpleXML a fair amount lately, and have run into an
    issue a number of times with character encodings. Basically, if a string 
    has a mixture of UTF-8 and non-UTF-8 characters, SimpleXML barfs, claiming
    the "String could not be parsed as XML."
</p>
<p>
    I tried a number of solutions, hoping actually to automate it via mbstring
    INI settings; these schemes all failed. iconv didn't work properly. 
    The only thing that did work was to convert the encoding to latin1 -- but
    this wreaked havoc with actual UTF-8 characters.
</p>
<p>
    Then, through a series of trial-and-error, all-or-nothing shots, I stumbled
    on a simple solution. Basically, I needed to take two steps:
</p>
<ul>
    <li>Detect the current encoding of the string</li>
    <li>Convert that encoding to UTF-8</li>
</ul>
<p>which is accomplished with:</p>
<div class="example"><pre><code class="language-php">
$enc = mb_detect_encoding($xml);
$xml = mb_convert_encoding($xml, 'UTF-8', $enc);
</code></pre></div>
<p>
    The conversion is performed even if the detected encoding is UTF-8; the
    conversion ensures that <em>all</em> characters in the string are properly
    encoded when done.
</p>
<p>
    It's a non-intuitive solution, but it works! QED.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;
