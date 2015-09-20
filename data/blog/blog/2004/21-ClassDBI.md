---
id: 21-ClassDBI
author: matthew
title: 'Class::DBI'
draft: false
public: true
created: '2004-01-25T22:51:16-05:00'
updated: '2004-09-20T13:32:09-04:00'
tags:
    - perl
    - personal
---
I was reading a thread on the cgiapp mailing list today from several of the core
developers about developing a book on `CGI::Application`. In it, several mentioned
that it might/should center around `CGI::App` and a handful of oft-used modules.
One of those modules is
[Class::DBI](http://search.cpan.org/%7Etmtm/Class-DBI-0.95/lib/Class/DBI.pm).

I took a gander at `Class::DBI` over at CPAN, and it looks absolutely amazing,
and at the same time perhaps too abstract. Basically, you create a number of
packages and/or packages, one for each table you'll be using in your
application, and one to establish your basic connection. Then, each package
creates an object instance of the connection, and defines a number of
properties: the name of the table, the columns you'll be using, and then the
relations it has to other tables (
`has_a( col_name => 'Package::Name'); has_many( col_name => 'Package::Name'); might_have(col_name => 'Package::Name');`
) etc.

Then you use the module/packages you need in your script, and you can then use
object-oriented notation to do things like insert rows, update rows, search a
table, select rows, etc. And it looks fairly natural.

I like the idea of data abstraction like this. I see a couple issues, however:

1. I don't like the idea of one package per table; that gets so abstract as to
   make development come to a stand-still, especially during initial development.
   However, once development is sufficiently advanced, I could see doing this,
   particularly for large projects; it could vastly simplify many regular DBI
   calls.
2. I **like** using SQL. If I need to debug why something isn't working when I
   interact with the database, I want to have absolute control over the
   language. Abstracting the SQL means I don't have that fine-grained control
   that helps me debug.

So, for now, I'll stick with straight DBI…. but this is an interesting avenue to
explore.
