<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('21-ClassDBI');
$entry->setTitle('Class::DBI');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1075089076);
$entry->setUpdated(1095701529);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'perl',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    I was reading a thread on the cgiapp mailing list today from several of the
    core developers about developing a book on CGI::Application. In it, several
    mentioned that it might/should center around CGI::App and a handful of
    oft-used modules. One of those modules is <a href="http://search.cpan.org/%7Etmtm/Class-DBI-0.95/lib/Class/DBI.pm">Class::DBI</a>.
</p>
<p>
    I took a gander at Class::DBI over at CPAN, and it looks absolutely amazing,
    and at the same time perhaps too abstract. Basically, you create a number of
    packages and/or packages, one for each table you'll be using in your
    application, and one to establish your basic connection. Then, each package
    creates an object instance of the connection, and defines a number of
    properties: the name of the table, the columns you'll be using, and then the
    relations it has to other tables (<tt>has_a( col_name =>
        'Package::Name'); has_many( col_name => 'Package::Name');
        might_have(col_name => 'Package::Name');</tt>) etc.
</p>
<p>
    Then you use the module/packages you need in your script, and you can then
    use object-oriented notation to do things like insert rows, update rows,
    search a table, select rows, etc. And it looks fairly natural.
</p>
<p>
    I like the idea of data abstraction like this. I see a couple issues,
    however:
</p>
<ol>
    <li>I don't like the idea of one package per table; that gets so abstract as
    to make development come to a stand-still, especially during initial
    development. However, once development is sufficiently advanced, I could see
    doing this, particularly for large projects; it could vastly simplify many
    regular DBI calls.</li>
    <li>I <strong>like</strong> using SQL. If I need to debug why something
    isn't working when I interact with the database, I want to have absolute
    control over the language. Abstracting the SQL means I don't have that
    fine-grained control that helps me debug.</li>
</ol>
<p>
    So, for now, I'll stick with straight DBI... but this is an interesting
    avenue to explore.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;