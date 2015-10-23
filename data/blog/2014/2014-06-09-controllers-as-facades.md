---
id: 2014-06-09-controllers-as-facades
author: matthew
title: 'Better Understanding Controllers Through Basic Patterns'
draft: false
public: true
created: '2014-06-09T12:00:00-05:00'
updated: '2014-06-09T12:00:00-05:00'
tags:
    - mvc
    - patterns
    - php
    - programming
    - rails
---
[Paul M. Jones](http://paul-m-jones.com/) has started an interesting discussion
rethinking the
[MVC](http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller)
pattern as applied to the web, which he has dubbed [Action-Domain-Responder (ADR)](https://github.com/pmjones/mvc-refinement).
If you haven't given it a read yet, click the link and do that; this page will
still be sitting here waiting when you return.

I agree with a ton of it — heck, I've contributed to it a fair bit via
conversations with Paul. But there's been one thing nagging at me for a bit
now, and I was finally able to put it into words recently.

Controllers — Actions in ADR — can be explained as *facades*.

<!--- EXTENDED -->

Definitions
-----------

The design pattern *Facade* originates in the book "Design Patterns: Elements
of Reusable Object Oriented Software," written by Erich Gamma, Ralph Johnson,
Richard Helm, and John Vlissides. Over the years, that book has come to be
referred to as the "Gang of Four", referring to the four authors, and often
abbreviated as "GoF".

The Facade pattern itself is one of the seven structural design patterns
defined in the GoF. Structural design patterns are those that provide a
mechanism for defining the *relationships* between classes or objects in a
system. Specifically:

> Facade defines a simplifed interface to a complex system.

[Wikipedia has a general entry on the pattern](http://en.wikipedia.org/wiki/Facade_pattern)
as well, and provides some other general characteristics of a Facade:

- A Facade creates a convenience method around a set of operations, thus
  reducing the complexity of operations.
- A Facade reduces the immediate dependencies of the calling code (they call
  the Facade, not the underlying code).

Facade Example
--------------

As an example, let's consider the following workflow:

- Marshal some objects
- Munge some incoming data
- Call a validator
- If the data does not validate, raise an error
- Start a transaction
- Pass data to several different tables
- Commit the transaction
- Log the changes
- Email notifications

Now, we could just write the code:

```php
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
```

Straight-forward. But imagine if you needed to do this more than once. Or if
you wanted to re-use this logic in multiple places in your application. This is
a situation just waiting to go out-of-sync — and one where developers will come
to rely on cut-and-paste for doing it correctly.

A facade would wrap this logic:

```php
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
```

You would then instantiate the facade — likely using an
[Inversion of Control](http://en.wikipedia.org/wiki/Inversion_of_control)
container to inject the dependencies — and then invoke it:

```php
$dataTransaction->execute(array_merge_recursive($_POST, $_FILES));
```

This code fulfills the Facade pattern: we're no longer directly manipulating
dependencies, and we've simplified a complex set of operations to a single,
unified API.

Controllers and Actions
-----------------------

Hopefully you can see where I'm going with this.

> Controllers in MVC, and Actions in ADR, are best characterized as Facades.

You can define Controllers or Actions as Facades for the following operations:

- Marshaling arguments from the request.
- Invoking any domain/model logic, using arguments marshaled from the request.
- Marshaling and returning a response/responder.

I think characterizing Controllers and Actions as Facades has some huge
benefits. In both
[PHP](http://blog.astrumfutura.com/archives/373-The-M-in-MVC-Why-Models-are-Misunderstood-and-Unappreciated.html)
and [Rails](https://www.google.com/search?q=fat+controllers+rails), we've
witnessed the problems that arise from so-called "Fat Controllers" —
controllers that do a ton of work, making them untestable, unreadable,
non-reusable nightmares. If we think of them as Facades, specifically for the
three items noted above, we focus on the specific purpose they fulfill within
the system, giving us:

- Adherence to the [Single Responsibility Principle](http://en.wikipedia.org/wiki/Single_responsibility_principle)
- Adherence to the [Dependency Inversion Priniciple](http://en.wikipedia.org/wiki/Dependency_inversion_principle)
- Adherence to the [Law of Demeter](http://en.wikipedia.org/wiki/Law_Of_Demeter)
- Ability to unit test our Controllers and Actions (instead of requiring integration tests with complex configuration and setup)
- The possibility of [hierarchical MVC](http://en.wikipedia.org/wiki/Hierarchical_model%E2%80%93view%E2%80%93controller) (usually tacked on, or poorly implemented)
- Clarity of purpose when creating Controllers and Actions (do only those three things)

Defining them as Facades for these three specific operations means we push
logic into specific domains, achieving a proper
[separation of concerns](http://en.wikipedia.org/wiki/Separation_of_concerns).
Anything that falls outside those three operations gets pushed elsewhere:

- Models/Domains are invoked with the arguments marshaled from the request. If
  you find yourself calling many models, or manipulating the results returned
  by models, you need to create Facades in your model/domain layer.
- If you find yourself doing lots of work in creating your response, you need
  to create a Facade for marshaling the response (in ADR, that would mean
  encapsulating more logic in your Responder).

For me, thinking of Controllers and Actions as Facades has an additional
benefit: it describes rather complex *architectural* patterns in terms of
*basic design patterns*. I find the more I can reduce the complexity of a
definition, the more likely I will understand and use it correctly.

### Epilogue

Consider this post a *refinement* of the MVC and ADR architectural patterns — a
way of describing them in terms of more fundamental design patterns.

Also, this article is littered with links. Click them. Read them. Digest them.
Read the books they reference. Design and architectural patterns exist because
developers observed the patterns and gave them names; learn to recognize them
and apply them, at all levels of your application.
