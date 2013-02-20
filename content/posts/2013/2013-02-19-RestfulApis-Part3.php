<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2013-02-19-restful-apis-with-zf2-part-3');
$entry->setTitle('RESTful APIs with ZF2, Part 3');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2013-02-19 14:27', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2013-02-19 14:27', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'php',
  'rest',
  'http',
  'zf2',
  'zend framework',
));

/**
 * Outline
 *
 * - Why is documentation important?
 * - What should you document?
 *   - What endpoints are available
 *   - Which operations are available for each endpoint (OPTIONS/Allow dance)
 *   - What payloads each endpoint expects
 *   - What payloads each endpoint will return
 *   - What errors are likely, and how they will look
 * - Where and how should you document?
 *   - OPTIONS
 *     - At the very least, to report Allow'd operations
 *     - Demonstrate how to react via AbstractRestfulController
 *     - Demonstrate a listener that raises an error (and correct code) when 
 *       request method is not allowed.
 *     - Potentially to provide the documentation itself
 *   - Static endpoint, linked via Link header
 *     - Documentation is linked in every request
 *     - What format?
 *       - Text-only formats are nice when you consider cURL, HTTPie, and other 
 *         tools.
 *       - Whatever you want, really.
 *     - Use PhlySimplePage or Soflomo\Prototype to return a page of docs
 */

$body =<<<'EOT'
<p>
    In my <a href="/blog/2013-02-11-restful-apis-with-zf2-part-1.html">previous</a> 
    <a href="/blog/2013-02-13-restful-apis-with-zf2-part-2.html">posts</a>, I 
    covered basics of JSON hypermedia APIs using Hypermedia Application Language
    (HAL), and methods for reporting errors, including API-Problem and vnd.error.
</p>

<p>
    In this post, I'll be covering <em>documenting</em> your API -- techniques 
    you can use to indicate what HTTP operations are allowed, as well as convey 
    the full documentation on what endpoints are available, what they accept, 
    and what you can expect them to return.
</p>

<p>
    While I will continue covering general aspects of RESTful APIs in this 
    post, I will also finally introduce several ZF2-specific techniques.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Why Document?</h2>

<p>
    If you're asking this question, you've either never consumed software, or
    your software is perfect. I frankly don't believe either one.
</p>

<p>
    In the case of APIs, those consuming the API need to know how to use it. 
</p>

<ul>
    <li>What endpoints are available? Which operations are available for each endpoint?</li>
    <li>What does each endpoint expect as a payload during the request?</li>
    <li>What can you expect as a payload in return?</li>
    <li>How will errors be communicated?</li>
</ul>

<p>
    While the promise of hypermedia APIs is that each response tells you the
    next steps available, you still, somewhere along the way, need more
    information - what payloads look like, which HTTP verbs should be used,
    and more. If you're <strong>not</strong> documenting your API, you're
    "doing it wrong."
</p>

<h2>Where Should Documentation Live?</h2>

<p>
    This is the much bigger question.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
