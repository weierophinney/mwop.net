---
id: 7-perl-cookbook-2nd-edition
author: matthew
title: 'Perl Cookbook, 2nd Edition'
draft: false
public: true
created: '2004-01-20T22:34:42-05:00'
updated: '2004-09-12T21:49:25-04:00'
tags:
    - personal
---
Tonight was Papa night, which meant that I got to look after Maeve while Jen
worked late doing a group at work. Last week, Maeve and I established that Papa
Night would always include going to the bookstore, which means Barnes &amp; Noble
in South Burlington.

Last week, Maeve was perfectly content to look at books by herself, and didn't
want me interfering, so I decided this week to grab a book for myself to peruse
while she was busy. It didn't work as I intended — Maeve saw that I wasn't
paying full attention to her, and then demanded my attention — but I was able
to look through some of the new items in the second edition of *The Perl
Cookbook*.

Among them were:

- Setting up both an XML-RPC server and client, using `SOAP::Lite`
- Setting up both a SOAP-RPC server and client, using `SOAP::Lite` and other
  modules; I could have used this in `ROX::Filer` to communicate with ROX
  instead of using the filer's RPC call.
- Better coverage of DBI (it actually covered it!):
  - When you expect only a single row, this is a nice way to grab it:

    ```perl
    $row = $dbi->selectrow_(array|hash)ref($statement)
    ```

  - This is a great way to grab a bunch of columns from a large resultset:

    ```perl
    $results = $dbi->selectall_hashref($sql);
    foreach $record (keys(%{$results})) {
        print $results->{$record}{fieldname};
    }
    ```
                        
  - This one is nice for a large resultset from which you only want one column:

    ```perl
    $results = $dbi->selectcol_arrayref($sql);
    foreach $result (@{$results}) {
        print $result;
    }
    ```

  - If you need to quote values before inserting them, try:

    ```perl
    $quoted = $dbi->quote($unquoted);
    $sql = "UPDATE table SET textfield = $quoted";
    ```

  - If you need to check for errors, don't check with each `DBI` call; instead,
    wrap all of them in an eval statement:

    ```perl
    eval {
        $sth = $dbi->prepare($sql);
        $sth->do;
        while ($row = $sth->fetchrow_hashref) {
            ...
        }
    }
    if ($@) {
        print $DBI::errstr; 
    }
    ```                    

- Coverage of templating, including `Text::Template` (*very* interesting!)
- Whole new chapters on `mod_perl` and XML (including DOM!) which I didn't really even get to peruse.
- **autouse pragma**: if you use:

  ```perl
  use autouse Module::Name;
  ```

  perl will *use* the module at runtime instead of compiletime; basically, it
  only uses it if it actually needs it (i.e., if it encounters code that
  utilizes functionality from that module). It's a good way to keep down on the
  bloat — I should use this with librox-perl, and possibly with `CGI::App`.
