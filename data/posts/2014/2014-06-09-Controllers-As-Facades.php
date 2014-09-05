<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-06-09-controllers-as-facades');
$entry->setTitle('Better Understanding Controllers Through Basic Patterns');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-06-09 12:00', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2014-06-09 12:00', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'mvc',
  'patterns',
  'php',
  'programming',
  'rails',
));

$body =<<<'EOT'
<p>
    <a href="http://paul-m-jones.com/">Paul M. Jones</a> has started an interesting
    discussion rethinking the <a href="http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller">MVC</a>
    pattern as applied to the web, which he has dubbed <a href="https://github.com/pmjones/mvc-refinement">Action-Domain-Responder (ADR)</a>.
    If you haven't given it a read yet, click the link and do that; this page will
    still be sitting here waiting when you return.
</p>

<p>
    I agree with a ton of it &#8212; heck, I've contributed to it a fair bit via conversations
    with Paul. But there's been one thing nagging at me for a bit now, and I was
    finally able to put it into words recently.
</p>

<p>
    Controllers &#8212; Actions in ADR &#8212; can be explained as <em>facades</em>.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Definitions</h2>

<p>
    The design pattern <em>Facade</em> originates in the book "Design Patterns: Elements of Reusable
    Object Oriented Software," written by Erich Gamma, Ralph Johnson, Richard Helm, and John
    Vlissides. Over the years, that book has come to be referred to as the "Gang of Four",
    referring to the four authors, and often abbreviated as "GoF".
</p>

<p>
    The Facade pattern itself is one of the seven structural design patterns defined in the GoF.
    Structural design patterns are those that provide a mechanism for defining the <em>relationships</em>
    between classes or objects in a system. Specifically:
</p>

<blockquote>
    Facade defines a simplifed interface to a complex system.
</blockquote>

<p>
    <a href="http://en.wikipedia.org/wiki/Facade_pattern">Wikipedia has a general entry on the pattern</a>
    as well, and provides some other general characteristics of a Facade:
</p>

<ul>
    <li>A Facade creates a convenience method around a set of operations, thus
        reducing the complexity of operations.</li>
    <li>A Facade reduces the immediate dependencies of the calling code (they call the Facade, not the underlying code).
</ul>

<h2>Facade Example</h2>

<p>
    As an example, let's consider the following workflow:
</p>

<ul>
    <li>Marshal some objects</li>
    <li>Munge some incoming data</li>
    <li>Call a validator</li>
    <li>If the data does not validate, raise an error</li>
    <li>Start a transaction</li>
    <li>Pass data to several different tables</li>
    <li>Commit the transaction</li>
    <li>Log the changes</li>
    <li>Email notifications</li>
</ul>

<p>
    Now, we could just write the code:
</p>

<div class="example"><pre><code language="php">
$db     = new Db($connectionConfig);
$log    = new Logger($loggerConfig);
$mailer = new Mailer($mailerConfig);
$data   = array_merge_recursive($_POST, $_FILES);

$inputFilter = new InputFilter();
$inputFilter->setData($data);
if (! $inputFilter->isValid()) {
    throw new DomainException();
}

$db->transactionStart();
$db->insertInto(/* ... */);
$db->insertInto(/* ... */);
$db->insertInto(/* ... */);
$db->transactionStop();

$log->info('Finished a transaction');
$mailer->send('New transaction')
</code></pre></div>

<p>
    Straight-forward. But imagine if you needed to do this more than once. Or if
    you wanted to re-use this logic in multiple places in your application. This
    is a situation just waiting to go out-of-sync &#8212; and one where developers
    will come to rely on cut-and-paste for doing it correctly.
</p>

<p>
    A facade would wrap this logic:
</p>

<div class="example"><pre><code language="php">
class DataTransaction
{
    protected $db;
    protected $logger;
    protected $mailer;

    public function __construct(Db $db, Logger $logger, Mailer $mailer)
    {
        $this->db     = $db;
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public function execute($data)
    {
        $inputFilter = new InputFilter();
        $inputFilter->setData($data);
        if (! $inputFilter->isValid()) {
            throw new DomainException();
        }

        $this->db->transactionStart();
        $this->db->insertInto(/* ... */);
        $this->db->insertInto(/* ... */);
        $this->db->insertInto(/* ... */);
        $this->db->transactionStop();

        $this->logger->info('Finished a transaction');
        $this->mailer->send('New transaction')
    }
}
</code></pre></div>

<p>
    You would then instantiate the facade &#8212; likely using an
    <a href="http://en.wikipedia.org/wiki/Inversion_of_control">Inversion of Control</a>
    container to inject the dependencies &#8212; and then invoke it:
</p>

<div class="example"><pre><code language="php">
$dataTransaction->execute(array_merge_recursive($_POST, $_FILES));
</code></pre></div>

<p>
    This code fulfills the Facade pattern: we're no longer directly manipulating dependencies,
    and we've simplified a complex set of operations to a single, unified API.
</p>

<h2>Controllers and Actions</h2>

<p>
    Hopefully you can see where I'm going with this.
</p>

<blockquote>
    Controllers in MVC, and Actions in ADR, are best characterized as Facades.
</blockquote>

<p>
    You can define Controllers or Actions as Facades for the following operations:
</p>

<ul>
    <li>Marshaling arguments from the request.</li>
    <li>Invoking any domain/model logic, using arguments marshaled from the request.</li>
    <li>Marshaling and returning a response/responder.</li>
</ul>

<p>
    I think characterizing Controllers and Actions as Facades has some huge benefits.
    In both <a href="http://blog.astrumfutura.com/archives/373-The-M-in-MVC-Why-Models-are-Misunderstood-and-Unappreciated.html">PHP</a>
    and <a href="https://www.google.com/search?q=fat+controllers+rails">Rails</a>,
    we've witnessed the problems that arise from so-called "Fat Controllers" &#8212;
    controllers that do a ton of work, making them untestable, unreadable, non-reusable
    nightmares. If we think of them as Facades, specifically for the three items
    noted above, we focus on the specific purpose they fulfill within the system,
    giving us:
</p>

<ul>
    <li>Adherence to the <a href="http://en.wikipedia.org/wiki/Single_responsibility_principle">Single Responsibility Principle</a></li>
    <li>Adherence to the <a href="http://en.wikipedia.org/wiki/Dependency_inversion_principle">Dependency Inversion Priniciple</a></li>
    <li>Adherence to the <a href="http://en.wikipedia.org/wiki/Law_Of_Demeter">Law of Demeter</a></li>
    <li>Ability to unit test our Controllers and Actions (instead of requiring integration tests with complex configuration and setup)</li>
    <li>The possibility of <a href="http://en.wikipedia.org/wiki/Hierarchical_model%E2%80%93view%E2%80%93controller">hierarchical MVC</a> (usually tacked on, or poorly implemented)</li>
    <li>Clarity of purpose when creating Controllers and Actions (do only those three things)</li>
</ul>

<p>
    Defining them as Facades for these three specific operations means we push logic
    into specific domains, achieving a proper <a href="http://en.wikipedia.org/wiki/Separation_of_concerns">separation of concerns</a>.
    Anything that falls outside those three operations gets pushed elsewhere:
</p>

<ul>
    <li>Models/Domains are invoked with the arguments marshaled from the request. If you find yourself
        calling many models, or manipulating the results returned by models, you need to create
        Facades in your model/domain layer.</li>
    <li>If you find yourself doing lots of work in creating your response, you need to create
        a Facade for marshaling the response (in ADR, that would mean encapsulating more logic
        in your Responder).
</ul>

<p>
    For me, thinking of Controllers and Actions as Facades has an additional benefit:
    it describes rather complex <em>architectural</em> patterns in terms of <em>basic design
    patterns</em>. I find the more I can reduce the complexity of a definition, the more
    likely I will understand and use it correctly.
</p>

<h3>Epilogue</h3>

<p>
    Consider this post a <em>refinement</em> of the MVC and ADR architectural 
    patterns &#8212; a way of describing them in terms of more fundamental design patterns.
</p>

<p>
    Also, this article is littered with links. Click them. Read them. Digest them. Read the books
    they reference. Design and architectural patterns exist because developers observed the
    patterns and gave them names; learn to recognize them and apply them, at all levels of
    your application.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
