<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('7-Perl-Cookbook,-2nd-Edition');
$entry->setTitle('Perl Cookbook, 2nd Edition');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074656082);
$entry->setUpdated(1095040165);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
));

$body =<<<'EOT'
<p>
    Tonight was Papa night, which meant that I got to look after Maeve while Jen
    worked late doing a group at work. Last week, Maeve and I established that
    Papa Night would always include going to the bookstore, which means Barnes
    & Noble in South Burlington.
</p>
<p>
    Last week, Maeve was perfectly content to look at books by herself, and
    didn't want me interfering, so I decided this week to grab a book for myself
    to peruse while she was busy. It didn't work as I intended -- Maeve saw that
    I wasn't paying full attention to her, and then demanded my attention -- but
    I was able to look through some of the new items in the second edition of
    <em>The Perl Cookbook</em>.
</p>
<p>
    Among them were:
</p>
<ul>
    <li>Setting up both an XML-RPC server and client, using SOAP::Lite</li>
    <li>Setting up both a SOAP-RPC server and client, using SOAP::Lite and other
        modules; I could have used this in ROX::Filer to communicate with ROX
        instead of using the filer's RPC call.
    </li>
    <li>Better coverage of DBI (it actually covered it!):
        <ul>
            <li>When you expect only a single row, this is a nice way to grab
                it:
                <pre>$row = $dbi->selectrow_(array|hash)ref($statement)
                </pre>
            </li>
            <li>This is a great way to grab a bunch of columns from a large
                resultset:
                <pre>                    $results = $dbi->selectall_hashref($sql);
                    foreach $record (keys(%{$results})) {
                        print $results->{$record}{fieldname};
                    }
                </pre>
            </li>
            <li>This one is nice for a large resultset from which you only want
                one column:
                <pre>                    $results = $dbi->selectcol_arrayref($sql);
                    foreach $result (@{$results}) {
                        print $result;
                    }
                </pre>
            </li>
            <li>If you need to quote values before inserting them, try:
                <pre>                    $quoted = $dbi->quote($unquoted);
                    $sql = "UPDATE table SET textfield = $quoted";
                </pre>
            </li>
            <li>If you need to check for errors, don't check with each DBI call;
                instead, wrap all of them in an eval statement:
                <pre>                    eval {
                        $sth = $dbi->prepare($sql);
                        $sth->do;
                        while ($row = $sth->fetchrow_hashref) {
                            ...
                        }
                    }
                    if ($@) {
                        print $DBI::errstr; 
                    }
                </pre>
            </li>
        </ul>
    </li>
    <li>Coverage of templating, including Text::Template (<em>very</em>
        interesting!)
    </li>
    <li>Whole new chapters on mod_perl and XML (including DOM!) which I didn't
        really even get to peruse.
    </li>
    <li><strong>autouse pragma</strong>: if you use:
        <pre>use autouse Module::Name;</pre>
        perl will <em>use</em> the module at runtime instead of compiletime;
        basically, it only uses it if it actually needs it (i.e., if it
        encounters code that utilizes functionality from that module). It's a
        good way to keep down on the bloat -- I should use this with
        librox-perl, and possibly with CGI::App.
    </li>
</ul>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;