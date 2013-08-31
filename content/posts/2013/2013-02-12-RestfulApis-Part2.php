<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2013-02-13-restful-apis-with-zf2-part-2');
$entry->setTitle('RESTful APIs with ZF2, Part 2');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2013-02-13 07:40', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2013-02-13 07:40', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'php',
  'rest',
  'http',
  'zf2',
  'zend framework',
));

$body =<<<'EOT'
<p>
    In my <a href="/blog/2013-02-11-restful-apis-with-zf2-part-1.html">last post</a>,
    I covered some background on REST and the Richardson Maturity Model, and some
    emerging standards around hypermedia APIs in JSON; in particular, I outlined
    aspects of Hypermedia Application Language (HAL), and how it can be used to
    define a generic structure for JSON resources.
</p>

<p>
    In this post, I cover an aspect of RESTful APIs that's often overlooked:
    reporting problems.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Background</h2>

<p>
    APIs are useful when they're working. But when they fail, they're only 
    useful if they provide us with meaningful information; if all I get is
    a status code, and no indication of what caused the issue, or where I might
    look for more information, I get frustrated.
</p>

<p>
    In consuming APIs, I've come to the following conclusions:
</p>

<ul>
    <li>
        Error conditions need to provide detailed information as to what went 
        wrong, and what steps I may be able to take next. An error code with
        no context gives me nothing to go on.
    </li>

    <li>
        Errors need to be reported consistently. Don't report the error one way
        one time, and another way the next.
    </li>

    <li>
        <strong>DO</strong> use HTTP status codes to indicate an error happened.
        Nothing is more irksome than getting back a 200 status with an error 
        payload.
    </li>

    <li>
        Errors should be reported in a format I have indicated I will Accept 
        (as in the HTTP header). Perhaps the only think more irksome than a 200
        status code for an error is getting back an HTML page when I expect 
        JSON.
    </li>
</ul>

<h2>Why Status Codes Aren't Enough</h2>

<p>
    Since REST leverages and builds on HTTP, an expedient solution for reporting
    problems is to simply use <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html">HTTP status codes</a>. 
    These are well understood by web developers, right?
</p>

<p>
    <code>4xx</code> error codes are errors made by the requestor, and are actually fairly 
    reasonable to use for reporting things such as lack of authorization tokens,
    incomplete requests, unsupportable operations, or non-supported media types.
</p>

<p>
    But what happens when the error is on the server - because something has
    gone wrong such as inability to reach your persistence layer or credential
    storage? The <code>5xx</code> series of status codes is sparse and wholly unsuited to
    reporting errors of these types -- <em>though you'll likely still want to use
    a <code>500</code> status to report the failure</em>. But what do you present to the consumer
    so that they know whether or not to try again, or what to report to you
    so that you can fix the issue?
</p>

<p>
    A status code simply isn't enough information most of the time. Yes, you 
    want to define standard status codes so that your clients can perform
    reasonable branching, but you also need a way to communicate <em>details</em>
    to the end-user, so that they can log the information for themselves, display
    information to their own end-users, and/or report it back to you so you can
    do something to resolve the situation.
</p>

<h2>Custom Media Types</h2>

<p>
    The first step is to use a custom media type. Media types are typically 
    both a name as well as a structure -- and the latter is what we're after
    when it comes to error reporting. 
</p>

<p>
    If we return a response using this media type, the client then knows how
    to parse it, and can then process it, log it, whatever.
</p>

<p>
    Sure, you can make up your own format -- as long as you are consistent
    in using it, and you document it. But personally, I don't like inventing
    new formats when standard formats exist already. Custom formats mean that 
    custom clients are required for working with the services; using a standard
    format can save effort and time.
</p>

<p>
    In the world of JSON, I've come across two error media types that appear to
    be gaining traction: <code>application/api-problem+json</code> and 
    <code>application/vnd.error+json</code>
</p>

<h3>API-Problem</h3>

<p>
    This particular media type is <a 
    href="http://tools.ietf.org/html/draft-nottingham-http-problem-02">via the 
    IETF</a>. Like HAL, it provides formats in both JSON and XML, making it 
    a nice cross-platform choice.
</p>

<p>
    As noted already, the media type is <code>application/api-problem+json</code>.
    The representation is a single resource, with the following properties:
</p>

<ul>
    <li>
        <strong>describedBy</strong>: a URL to a document describing the error condition (required)
    </li>

    <li>
        <strong>title</strong>: a brief title for the error condition (required)
    </li>

    <li>
        <strong>httpStatus</strong>: the HTTP status code for the current request (optional)
    </li>

    <li>
        <strong>detail</strong>: error details specific to this request (optional)
    </li>

    <li>
        <strong>supportId</strong>: a URL to the specific problem occurrence (e.g., to a log message) (optional)
    </li>
</ul>

<p>
    As an example:
</p>

<div class="example"><pre><code language="http">
HTTP/1.1 500 Internal Error
Content-Type: application/api-problem+json

{
    "describedBy": "http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html",
    "detail": "Status failed validation",
    "httpStatus": 500,
    "title": "Internal Server Error"
}
</code></pre></div>

<p>
    The specification allows a large amount of flexibility -- you can have your 
    own custom error types, so long as you have a description of them to link 
    to. You can provide as little or as much detail as you want, and even 
    decide what information to expose based on environment.
</p>

<p>
    I personally like to point to the HTTP status code definitions, and then 
    provide request-specific detail; I find this gives quick and simple results 
    that I can later shape as I add more detail to my API. However, the 
    specification definitely encourages you to have unique error types with 
    discrete URIs that describe them -- never a bad thing when creating APIs.
</p>

<h3>vnd.error</h3>

<p>
    This is a <a href="https://github.com/blongden/vnd.error">proposed media 
    type</a> within the HAL community. Like HAL, it provides formats in both 
    JSON and XML, making it a nice cross-platform choice.
</p>

<p>
    It differentiates from API-Problem in a few ways. First, it allows, and 
    even encourages, reporting collections of errors. If you consider PHP 
    exceptions and the fact that they support "previous" exceptions, this is a 
    powerful concept; you can report the entire chain of errors that led to the 
    response.  Second, it encourages pushing detail out of the web service; 
    errors include a "logRef" property that points to where the error detail lives. 
    This is probably better illustrated than explained.
</p>

<p>
    The response payload is an array of objects. Each object has the following 
    members:
</p>

<ul>
    <li>
        <strong>logRef</strong>: a unique identifier for the specific error which can then be
        used to identify the error within server-side logs (required)
    </li>

    <li>
        <strong>message</strong>: the error message itself (required)
    </li>

    <li>
        <strong>_links</strong>: HAL-compatible links. Typically, "help", "describes", and/or 
        "describedBy" relations will be defined here.
    </li>
</ul>

<p>
    As an example, let's consider the API-Problem example I had earlier, and
    provide a vnd.error equivalent:
</p>

<div class="example"><pre><code language="http">
HTTP/1.1 500 Internal Error
Content-Type: application/vnd.error+json

[
    {
        "logRef": "someSha1HashMostLikely",
        "message": "Status failed validation",
        "_links": {
            "describedBy": {"href": "http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html"}
        }
    }
]
</code></pre></div>

<p>
    vnd.error basically begs you to create custom error types, with documentation 
    end-points that detail the source of the error and what you can do about it 
    (this is true of API-Problem as well).
</p>

<p>
    The requirement to include a log reference ("logRef") and have it be unique 
    can be a stumbling block to implementation, however, as it requires effort 
    for uniquely identifying requests, and logging. However, both the 
    identification and logging can be automated.
</p>

<h2>Summary</h2>

<p>
    Error reporting in APIs is as important as the normal resource payloads 
    themselves. Without good error reporting, when an API raises errors, 
    clients have difficulty understanding what they can do next, and cannot
    provide you, the API provider, with information that will allow you to
    debug on the server side.
</p>

<p>
    As noted at the beginning of the article, if you follow the rules below,
    you'll make consumers of your API happier and more productive.
</p>

<ul>
    <li>
        <strong>DO</strong> use appropriate HTTP status codes to indicate an 
        error happened.
    </li>

    <li>
        Report errors in a format I have indicated I will Accept 
        (as in the HTTP header). 
    </li>
    <li>
        Report errors consistently. Don't report the error one way one time, 
        and another way the next. Standardize on a specific error-reporting 
        media type .  While you <em>can</em> create your own error structure, I 
        recommend using documented, accepted standards. This will make clients 
        more re-usable, and make many of your decisions for you.
    </li>

    <li>
        Provide detailed information as to what went wrong, and what steps I 
        may be able to take next. Provide documentation for each type of error, 
        and link to that documentation from your error payloads.
    </li>

</ul>

<p>
    Which brings me to...
</p>

<h2>Next time</h2>

<p>
    I realize I still haven't covered anything specific to ZF2, but I'll start 
    next time, when I cover the next topic: documenting your API. An 
    undocumented API is a useless API, so it's good to start baking 
    documentation in immediately. I'll survey some of the possibilities and how 
    they can be implemented in ZF2 in the next installment, and then we can get 
    our hands dirty with actual API development.
</p>

<h3>Updates</h3>

<p>
    <em>Note: I'll update this post with links to the other posts in the series 
    as I publish them.</em>
</p>

<ul>
    <li><a href="/blog/2013-02-11-restful-apis-with-zf2-part-1.html">Part 1</a></li>
    <li><a href="/blog/2013-02-25-restful-apis-with-zf2-part-3.html">Part 3</a></li>
</ul>

EOT;
$entry->setExtended($extended);

return $entry;
