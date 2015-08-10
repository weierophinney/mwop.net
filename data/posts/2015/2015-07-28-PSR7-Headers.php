<?php // @codingStandardsIgnoreFile
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2015-07-28-on-psr7-headers');
$entry->setTitle('On PSR7 and HTTP Headers');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2015-07-28 09:00', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2015-07-28 09:00', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'http',
  'php',
  'programming',
  'psr-7'
));

$body =<<<'EOT'
<p>
  Yesterday, a question tagged #psr7 on Twitter caught my eye:
</p>

<blockquote>
  <p>
    #psr7 Request::getHeader($name) return array of single string instead of
    strings in #Slim3? cc: @codeguy pic.twitter.com/ifA9hCKAPs
  </p>

  <footer>
    <cite>
      <a href="https://twitter.com/feryardiant">@feryardiant</a>
      (<a href="https://twitter.com/feryardiant/status/624705995097247744">tweet</a>)
    </cite>
  </footer>
</blockquote>

<p>
  The image linked provides the following details:
</p>

<blockquote>
  <p>
    When I call <code>$request-&gt;getHeader('Accept')</code> for example, I was expected
    that I'll get something like this:
  </p>

  <pre><code class="language-php">
Array(
    [0] => text/html,
    [1] => application/xhtml+xml,
    [2] => application/xml,
)
  </code></pre>

  <p>
    but, in reallity I got this:
  </p>

  <pre><code class="language-php">
Array(
    [0] => text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
)
  </code></pre>

  <p>
    Is it correct?
  </p>
</blockquote>

<p>
  In this post, I'll explain why the behavior observed is correct, as well as
  shed a light on a few details of header handling in
  <a href="http://www.php-fig.org/psr/psr-7/">PSR-7</a>.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Headers in PSR-7</h2>

<p>
  When creating the PSR-7 specification, we had to juggle a fair number of
  details from the various HTTP specifications. Headers are one area that is
  particularly difficult, due to the flexibility and ambiguity in the
  specification.
</p>

<p>
  The root of the ambiguity is that headers are allowed to have multiple values.
  Headers <em>may</em> have multiple values, but it's up to the specification
  for any given header.
</p>
  
<p>
  Additionally how multiple values are represented is up to the given header.
  The HTTP specifications allow using multiple invocations for the same header:
</p>

<pre><code class="language-http">
X-Foo-Bar: baz
X-Foo-Bar: bat
</code></pre>

<p>
  The above would mean that the <code>X-Foo-Bar</code> header has two values,
  <code>baz</code> and <code>bat</code>. Assuming the header allows multiple
  values at all; if it doesn't, then it has a single value, and the last
  representation wins (<code>bat</code>, if you're paying attention).
</p>

<p>
  The other way to represent multiple values is using a separator. The
  specifications indicate that if you want to have multiple values in the
  same header line, you <code>should</code> use a comma (<code>,</code>) as a
  separator. However, you <code>may</code> use any other separator you want.
  The <code>SetCookie</code> header is a prime example of a header allowing
  multiple values that uses a completely different separator (semicolon)!
</p>

<p>
  So, to summarize:
</p>

<ul>
  <li>A header may or may not allow multiple values.</li>
  <li>Headers may be emitted more than once. If a header allows multiple values,
    then its value is the aggregate of each representation. If the header only
    allows one value, the last representation is the canonical value for that
    header.</li>
  <li>Headers may use a separator character in a single line in order to
    separate multiple values. That character is suggested to be a comma, but it
    can vary per-header.</li>
</ul>

<p>
  The other big ambiguity in the specification is that the specification is
  <em>extensible</em>, and specifically allows for <em>custom</em> headers.
</p>

<p>
  This means that any general-purpose code representing HTTP, such as PSR-7,
  cannot possibly know the entire ruleset governing all possible HTTP messages,
  as it cannot know all potential header types, including whether they allow
  multiple values or not.
</p>

<p>
  With these two facts in mind &#8212; headers <em>may</em> have multiple
  values, and <em>custom</em> headers are allowed &#8212; we made the following
  decisions with PSR-7:
</p>

<h3>All headers are collections</h3>

<p>
  All headers are assumed to have multiple values. This gives consistency of
  usage, and puts the onus of knowing the semantics of any given header to the
  consumer.
</p>

<p>
  For that reason, the most basic access for a given header,
  <code>getHeader($name)</code>, returns an array. That array can have the
  following values:
</p>

<ul>
  <li>It can be empty; this means the header was not, or will not be, present in
    the representation.</li>
  <li>A single string value.</li>
  <li>More than one string value.</li>
</ul>

<h3>Naive Concatenation</h3>

<p>
  Since the majority of headers only allow single values, and since most
  existing libraries that parse headers only accept strings, we provided another
  method, <code>getHeaderLine($name)</code>. This method guarantees return of a
  string:
</p>

<ul>
  <li>If the header has no values, the string will be empty.</li>
  <li>Otherwise, it concatenates the values using a comma.</li>
</ul>

<p>
  We chose <em>not</em> to provide an argument indicating the separator to use,
  as the specification only indicates commas as separators, but also to reduce
  complexity of implementations. If you want to use a different separator, you
  can do so yourself using <code>implode($separator,
    $message->getHeader($name))</code>.
</p>

<h3>No Parsing</h3>

<p>
  Because separator characters vary per-header, and because different headers
  have different specifications regarding how to interpret the data, and because
  the specification allows custom headers we cannot code for in a
  general-purpose library, we decided that PSR-7 implementations <em>must
  not</em> parse header values provided to them.
</p>

<p>
  Practically this has two effects:
</p>

<ul>
  <li>For incoming requests, even if a header allows multiple comma-separated
    values, implementations must leave them intact. This ensures no data-loss.</li>
  <li>For complex values, you must pass them to a parser to decompose and
    interpret them.</li>
</ul>

<p>
  The rule also has another motivation: to provide a semantic for emitting
  headers with multiple values as either a single line or as multiple lines. If
  all values are concatenated in a single line, the client or server can assume
  that the message should be sent or was received with the header as a single
  line, while an array of multiple lines would indicate multiple header lines.
  This allows the <em>consumer</em> to decide how the header should be
  represented!
</p>

<h3>Ramifications</h3>

<p>
  The path we chose has some interesting ramifications. First, we ended up with
  a highly consistent API. There's no ambiguity in terms of what data types I
  can expect when I call <code>getHeader()</code> or
  <code>getHeaderLine()</code>. Second, I can be assured that there has been no
  data loss once I have the results of one of those operations; no process has
  attempted to parse the value and potentially alter it.
</p>

<p>
  The flip side is the Twitter comment from earlier. Let's look at that again.
</p>

<h2>Breaking it Down</h2>

<p>
  Let's revisit what the author received from a <code>getHeader('Accept')</code>
  call:
</p>

<pre><code class="language-php">
Array(
    [0] => text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
)
</code></pre>

<p>
  The <code>Accept</code> header allows multiple values, but expects them as a
  single comma-concatenated string. Contrary to what the author expected, the
  above represents the following values:
</p>

<pre><code class="language-php">
[
    'text/html',
    'application/xhtml+xml',
    'application/xml;q=0.9',
    'image/web',
    '*/*;q=0.8',
]
</code></pre>

<p>
  Note that the values include the <code>;q=*</code> notations inline! The
  <code>Accept</code> header separates values with commas, but each value can
  have additional key/value attributes separated by semicolons as well.
</p>

<p>
  Why isn't the above what we get from <code>getHeader()</code>? It goes back to
  the last rule I mentioned regarding PSR-7 header treatment: <strong>no
  parsing</strong>. The Accept header specification indicates that multiple
  values should be on the same line, separated by commas, and that's precisely
  how browsers send it to the server; PSR-7 takes the line as-is and sets it as
  the sole value in the array.
</p>

<h2>Recommendations</h2>

<p>
  The above example provides another good lesson: Complex values should have
  dedicated parsers. PSR-7 literally only deals with the low-level details of an
  HTTP message, and provides no interpretation of it. Some header values, such
  as the <code>Accept</code> header, require dedicated parsers to make sense of
  the value.
</p>

<p>
  What does the value indicate?
</p>

<ul>
  <li>The client prefers <code>text/html</code>,
    <code>application/xhtml+xml</code>, and <code>image/webp</code>
    representations when possible; if any of those three are available, they are
    preferred <em>in that order</em></li>
  <li>If none of the above are available, the next representation preferred is
    <code>application/xml</code>.</li>
  <li>Any other representation may be returned otherwise.</li>
</ul>

<p>
  How do I know this? By reading the <a
    href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html">Accept header
  specification</a>. Which is ridiculously complex. And for which a number of
  libraries are already written, <em>which can accept the Accept header value,
  parse it, and return the priority queue for you</em>. <strong>PSR-7 acts as 
  the data source for such libraries, but does no parsing itself.</strong>
</p>

<h2>Fin</h2>

<p>
  Hopefully, this post has demystified how PSR-7 represents and handles HTTP
  headers. PSR-7 was designed to mirror the extensibility of the HTTP
  specifications, provide consistency of usage, and data integrity.
</p>

<p>
  One specific recommendation we made in the metadocument was that any
  processing of headers be delegated to dedicated libraries. I'm hoping to see
  more of these spring up as we see PSR-7 adoption ramp up.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
