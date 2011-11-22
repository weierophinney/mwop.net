<?php
use Blog\EntryEntity,
    Zend\Loader\AutoloaderFactory;

require_once 'Zend/Loader/AutoloaderFactory.php';
require __DIR__ . '/../../CommonResource/autoload_register.php';
require __DIR__ . '/../autoload_register.php';

$entry = new EntryEntity();

$entry->setTitle('New site!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setTags(array('php', 'personal'));

$body =<<<'EOT'
<p>
    After blogging for almost eight years, I've finally updated my blog and 
    site beyond mere cosmetic changes. The result is an effort that has spanned 
    a couple of years, several rewrites, and often changing paradigms in how I
    think about programming. Keep reading for details.
</p>
EOT;
$entry->setBody($body);

$extended =<<<'EOT'
<h2>Domain Modelling and Documents</h2>

<p>
    I've spoken on the subject of domain modeling at conferences a number of 
    times, and my evolving thoughts on the subject were the original impetus
    for rewriting my site. I decided early on to use a blog, <em>my blog</em>,
    as the non-trivial example for working with models. Why? Because you get 
    to solve some non-trivial problems that still have limited scope.
</p>

<p>
    Something I quickly realized as I started looking at domain modeling is that
    a lot of <em>web content</em> really isn't relational. I'm talking 
    specifically web <em>content</em> here -- throwing pages up for people to
    read and comment on. These are simply structured documents.
    Heck, even for the relational parts, such as commenting (where you may have
    relations between parent and child comments), SQL and RDBM systems often
    get more in the way than provide convenience.
</p>

<p>
    So, I adopted document oriented databases for my blog. I tried both CouchDB
    and MongoDB before settling on the latter -- largely because I found the
    PHP extension's API so <em>usable</em>, but also because the query language
    made some of the simple things (like returning all entries by year, month, 
    or day), well, simple. But both are compelling technologies, and both do 
    exactly what I think they should do: solve the web content problem.
</p>

<p>
    One other thing I realized this past year: there's absolutely no reason for
    me to write my own commenting system. With tools like Disqus, I can retain
    ownership of my content and interactions, while greatly simplifying my site
    and programming tasks. This also means that the choice of a document 
    database makes even more sense -- since I've eliminated yet one more 
    relationship within the content.
</p>

<h2>Early Adopter</h2>

<p>
    I'd written the domain layer quite some time ago. In fact, it was just 
    sitting there in my repository, lingering, waiting for me to do something
    with it.
</p>

<p>
    Then I read about this "mustache" thing. A templating language that's both
    simple and elegant, and cross-platform. Except that the PHP implementation
    I ran into didn't quite do what I'd expect, and threw (at the time) a lot
    of notices and warnings. So, I wrote a mustache library for PHP.
</p>

<p>
    Exhilirated with creating something, I started applying it to the site.
</p>

<p>
    Some things were immensely easy. Injecting content and titles was insanely
    easy and fun. Formatting dates, however, was a lesson in patience that
    required careful planning.
</p>

<p>
    Much as I loved the initial simplicity and elegance of mustache, in the end,
    I decided PHP was even more simple and powerful, and I went back to it. Just
    in time to try something <em>really</em> new.
</p>

<p>
    Zend Framework 2.
</p>

<p>
    I'd created and released an MVC prototype, and then the community picked it
    up and ran with it, and things got <em>really</em> interesting and fun. So
    I took what I'd done with the domain layer of my site, and started wrapping
    discrete areas of functionality as ZF2 modules. I created a "CommonResource"
    module that I could consume for modules requiring domain modelling; my "Blog"
    module extends it. I created a "Comics" module for aggregating comics. I 
    created a "Contact" module for displaying and submitting my contact form.
    I created an "Authorization" module for implementing an opt-in 
    authorization layer over my site that modules could configure, and which
    includes an authentication form and handler. As I worked on these, I learned
    some of the issues with the prototype I'd created, as well as with the various
    components it uses, and was able to work with the other contributors to 
    make things better.
</p>

<p>
    This site is an initial non-trivial example of what ZF2 can do. I'll be 
    releasing the code, so others can learn some of the patterns for building
    ZF2 apps that I've discovered in the process.
</p>

<h2>More personalization and homogenization</h2>

<p>
    The previous site incarnation started as a blog, and gradually grew into a
    "resumé" site. However, because it started as a blog, like most blog software,
    it was difficult to add new application features. I either had to add them
    outside the blog, and duplicate the blog template, or I had to try and emulate
    the functionality via blog plugins. I feel a site should be integrated, and
    that the individual features should only return markup that can be injected
    into a site template.
</p>

<p>
    So, that was my approach this time around.
</p>

<p>
    I have a better C.V. section, and my footer tries to give hints as to who
    I am and what I do. Everything is tied together in a common site theme -- 
    the same fonts and styles and layout are used throughout. I have tried to 
    use whitespace as much as possible to convey semantic layout. I've retained 
    my grayscale theme, because I enjoy the irony of grayscale in the vibrant 
    internet. 
</p>

<h2>Welcome!</h2>

<p>
    So, welcome to my site! Feel free to explore, and let me know what works,
    and more importantly, what doesn't.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
