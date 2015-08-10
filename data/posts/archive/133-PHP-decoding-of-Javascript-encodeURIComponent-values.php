<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('133-PHP-decoding-of-Javascript-encodeURIComponent-values');
$entry->setTitle('PHP decoding of Javascript encodeURIComponent values');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1170265013);
$entry->setUpdated(1170354828);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Recently, I was having some issues with a site that was attempting to use
    UTF-8 in order to support multiple languages. Basically, you could enter
    UTF-8 characters -- for instance, characters with umlauts -- but they
    weren't going through to the web services or database correctly. After more
    debugging, I discovered that when I turned off javascript on the site, and
    used the degradable interface to submit the form via plain old HTTP,
    everything worked fine -- which meant the issue was with how we were sending
    the data via XHR.
</p>
<p>
    We were using <a href="http://prototypejs.org">Prototype</a>, and in
    particular, POSTing data back to our site -- which meant that the UI
    designer was using <kbd>Form.serialize()</kbd> to encode the data for
    transmission.  This in turn uses the javascript function
    <kbd>encodeURIComponent()</kbd> to do its dirty work.
</p>
<p>
    I tried a ton of things in PHP to decode this to UTF-8, before stumbling on 
    <a href="http://www.garayed.com/perl/218742-how-decode-javascripts-encodeuricomponent-perl.html">a solution written in Perl.</a>
    Basically, the solution uses a regular expression to grab urlencoded hex
    values out of a string, and then does a double conversion on the value,
    first to decimal and then to a character. The PHP version looks like this:
</p>
<div class="example"><pre><code class="language-php">
$value = preg_replace('/%([0-9a-f]{2})/ie', \&quot;chr(hexdec('\\1'))\&quot;, $value);
</code></pre></div>
<p>
    We have a method in our code to detect if the incoming request is via XHR.
    In that logic, once XHR is detected, I then pass <kbd>$_POST</kbd> through the
    following function:
</p>
<div class="example"><pre><code class="language-php">
function utf8Urldecode($value)
{
    if (is_array($value)) {
        foreach ($key =&gt; $val) {
            $value[$key] = utf8Urldecode($val);
        }
    } else {
        $value = preg_replace('/%([0-9a-f]{2})/ie', 'chr(hexdec($1))', (string) $value);
    }

    return $value;
}
</code></pre></div>
<p>
    This casts all UTF-8 urlencoded values in the $_POST array back to UTF-8,
    and from there we can continue processing as normal.
</p>
<p>
    Man, but I can't wait until PHP 6 comes out and fixes these unicode
    issues...
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;
