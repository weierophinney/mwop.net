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
 *     - Potentially to provide the documentation itself
 *   - Static endpoint, linked via Link header
 *     - Documentation is linked in every request
 *     - Use PhlySimplePage or Soflomo\Prototype to return a page of docs
 *     - What format?
 *       - Text-only formats are nice when you consider cURL, HTTPie, and other 
 *         tools.
 *       - Whatever you want, really.
 */

$body =<<<'EOT'
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
EOT;
$entry->setExtended($extended);

return $entry;
