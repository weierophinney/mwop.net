---
id: 2023-12-10-advent-dbal
author: matthew
title: 'Advent 2023: Doctrine DBAL'
draft: false
public: true
created: '2023-12-10T11:00:00-06:00'
updated: '2023-12-10T11:00:00-06:00'
tags:
    - advent2023
    - dbal
    - doctrine
    - php
---
I've mostly taken database abstraction for granted since I started at Zend.
We had a decent abstraction layer in ZF1, and improved it for ZF2.
There were a lot quirks to it — you really had to dive in and look at the various SQL abstraction classes to understand how to do more complex stuff — but it worked, and was always right there and available in the projects I worked on.

In the last couple of years, though, we came to the realization in the Laminas Project that we didn't really have anybody with the expertise or time to maintain it.
We've marked it security-only twice now, and while we've managed to keep it updated to each new PHP version, it's becoming harder and harder, and whenever there's a CI issue, it's anybody's guess as to whether or not we'll be able to get it resolved.

My alternatives have been straight PDO, or Doctrine DBAL, with the latter being my preference.

<!--- EXTENDED -->

### Doctrine _what_?

When most folks who use PHP hear "Doctrine", they immediately think "[ORM](https://en.wikipedia.org/wiki/Object%E2%80%93relational_mapping)"; it's how most folks use it, and what it's best known for.

Underlying the ORM is its database abstraction layer (hence "DBAL").
This library exposes an API that will work across any database it supports; this is essentially what zend-db, and later laminas-db, were doing as well.
What most folks don't realize is that you can use the DBAL _by itself_, without the ORM.

### Why no ORM?

ORMs are fine.
Really.
But they add an additional layer of complexity to understanding what you are actually doing.
Additionally, if you want to do something that doesn't quite fit how the ORM works, you'll need to drop down to the DBAL anyways.
So my take has always been: why not just use the DBAL from the beginning?

So, how does _Matthew_ write code that interacts with the database?

I start by writing value objects that represent discrete aspects of the application.
Most of my work will be in consuming or creating these.
From there, I write a _[repository](https://martinfowler.com/eaaCatalog/repository.html)_ class that I use for purposes of persisting and retrieving them.
I can usually extract an interface from this, which aids in my testing, or if I decide I need a different approach to persistence later.

I push the work of mapping the data from the database to these objects, and vice versa, either in the repository, or in the value objects themselves (often via a [named constructor](https://verraes.net/2014/06/named-constructors-in-php/)).
Using these approaches creates lean code that can be easily tested, and for which there's no real need to understand the underlying system; it's all right there in what I've written for the application.

### Some gripes about the documentation, and some tips

The [Doctrine DBAL docs](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/index.html) are a bit sparse, particularly when it comes to its [SQL abstraction](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/query-builder.html).
And there's no "getting started" or "basic usage" guide.
In fact, it's not until the third page within the docs that you get any code examples; thankfully, at that point they give you information on how to get a database connection:

```php
use Doctrine\DBAL\DriverManager;

$connectionParams = [
    'dbname'   => 'mydb',
    'user'     => 'user',
    'password' => 'secret',
    'host'     => 'localhost',
    'driver'   => 'pdo_mysql',
];
$conn = DriverManager::getConnection($connectionParams);
```

They also provide a number of other approaches, including using a DSN (an acronym they never explain, but based on using PDO, likely means "data source name").

Once you have a connection, what do you do?
Well the DBAL connection allows you to prepare and execute queries, including via the use of prepared statements.
It provides a variety of methods for fetching individual or multiple rows, with a variety of options for how the data is returned (indexed arrays, associative arrays, individual columns, individual values, etc.).
These retrieval methods are mirrored in the result instances returned when executing prepared statements as well.

And that brings me to the SQL abstraction.

First, it's really, really good.
It's minimal, but it covers just about anything you need to do.
If you need to write something complex, you probably can; the beauty is that if you can't, you can always fall back to a SQL query, and using the connection's API for binding values.

But the documentation could be better.

It felt like it was written by a database admin who has forgotten more than most people ever learn about databases, and never considered that others might not know as much as them.
The fact that it starts with architecture and not usage feels hugely antagonistic for somebody coming in just wanting to know how to connect to the database, build a query, and fetch some results.
(The irony is not lost on me that this is almost exactly how Laminas and Mezzio docs are written, and, yes, I recognize we could all do better!)

> Before folks start grousing, yes, I have on my TODO list an item for contributing to the DBAL docs.
> I'm trying to work up an outline of what I would have found useful, what acronyms need explanation, and some examples of common patterns before I make any suggestions, however.

First, they have a whole documentation page related to the SQL query builder, and a lot of examples.
But not a single one details _how to actually execute the query_!
So, for those wondering:

```php
$sql = $conn->createQueryBuilder();

// ... build your query ...

// Execute a query that will retrieve results (generally SELECT queries):
$result = $sql->executeQuery();

// Execute a query that produces changes (INSERT, UPDATE, DELETE, etc.):
$count = $sql->executeStatement();
```

Query results have a variety of `fetch*()` operations on them, while executing a statement returns an integer indicating the number of rows affected (assuming the database supports this).

Second, when I started doing joins, the argument names were confusing, and made it harder to understand what was needed.
I eventually figured it out, but it was really easy to flip the arguments for the different tables being joined.
The usage below illustrates names that would better describe how to use it:

```php
$sql->innerJoin(
    $primaryTableOrItsAliasIfYouSpecifiedOne, // e.g. "user" or "u"
    $newTableToJoin,                          // e.g. "address"
    $aliasForNewTableToJoin,                  // e.g. "a"
    $conditionToJoinOn                        // e.g. "u.id = a.uid"
);
```

Third, there's some odd differences in the API between INSERT and UPDATE operations.,
When setting a value, one takes `setValue()`, while the other takes `set()`, and only one of these is valid for a given operation (it's `setValue()` for INSERT operations, and `set()` for UPDATE operations, in case you were wondering).
This is especially confusing when using bound parameters, because _both_ can use the `setParameter()` method for binding positional placeholder values.

Speaking of plaeholders, the docs don't do a great job of detailing how to handle _placeholders_ gracefully.

The documentation suggests patterns like this:

```php
$queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->where('email = ?')
    ->setParameter(0, $userInputEmail);
```

Which is fine when there's only one parameterized value, but what if you have several, or if you're dynamically building the query (e.g., looping through user-supplied sorting or criteria, etc.), and you don't know their exact position in the final query?
And what if you want to use named parameters instead of positional parameters, but you're not sure if your database supports them?

The answer is in the docs, but the various _examples_ don't use the pattern (other than in the discussion of the methods), which is infuriating.
The above can also be written as follows:

```php
$queryBuilder
    ->select('id', 'name')
    ->from('users')
    ->where('email = ' . $queryBuilder->createNamedParameter($userInputEmail));
```

There's also a `createPositionalParameter()` method.
Both accept an optional second argument, where you can specify the value _type_, which can help ensure that values are quoted correctly for the SQL type they will map to.
This also allows you to do `IN()` operations, and each value will be quoted correctly, with the appropriate list separator for the database.

Once you know this approach, it's easy to remember and use, but it took me a few times through the docs before I stumbled across it.

The SQL it generates, though, is great, and when I've used tools like ZendHQ's Z-Ray to introspect queries, I'm always impressed by what was actually sent over the wire.

But for all these issues, the fact is that the docs generally give you _enough_, and the API is so clean and reasonably documented that you can generally figure out how things work just from your IDE hints and autocompletion.
Yes, I have gripes, but the library is _very_ solid, _very_ well written, and absolutely something I can depend on.

### Final Thoughts

I've often used straight PDO for projects, and it works fine.
However, having a tool available like Doctrine DBAL has been a huge boon in ensuring I can switch from SQLite while prototyping to MySQL for production, and know that things will "just work".

I also find the way it juggles _types_ to be really useful.
I know that if a value is typed in the database as a NULL or as text or as a float or integer, I'll actually get those types back when I query; the same is true for when I send data to the database.
There's no magic involved, and I don't have to remember to do type conversions to and from the database.
That's _exactly_ the type of functionality I want from a DBAL.

Yes, writing database-centric code is cumbersome, and there's a reason folks use ORMs, ActiveRecord, and the like.
However, it generally only needs to be written once, with occasional updates.
Having a good DBAL available helps keep complexity of your application down and gives you the tools to communicate securely with your database.
