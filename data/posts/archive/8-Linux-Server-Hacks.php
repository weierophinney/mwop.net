<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('8-Linux-Server-Hacks');
$entry->setTitle('Linux Server Hacks');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074660604);
$entry->setUpdated(1094869975);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
));

$body =<<<'EOT'
<p>
    I stopped at Borders in downtown Burlington on New Year's Eve day, and found
    a book called <em>Linux Server Hacks</em>. I loved it immediately, but I
    wasn't quite willing to shell out $25 for such a slim volume, even if it did
    have many tidbits I could immediately use.
</p>
<p>
    When I told my co-worker, Rob, about it, it turned out he already had the
    book, and brought it in to work for me to borrow the next day.
</p>
<p>
    My nose has barely been out of it since. I've done such things as:
</p>
<ul>
    <li>
        Create personal firewalls for my home and office machines. I've always
        used scripts for this, but the hacks for iptables showed the basics of
        how they work, and I've now got nice robust firewalls that are very
        simple scripts. To make them even more user-friendly, I borrowed some
        syntax from the various /etc/init.d scripts so that I can start, stop,
        and reload the firewall at will.
    </li>
    <li>
        I don't use perl at the command line much, even though I've long known
        the '-e' switch; it just seems to cumbersome. However, combine it with
        the '-p' and/or '-i' switch, and you can use perl as a filter on globbed
        files!
    </li>
    <li>
        I know <strong>much</strong> more about SSH now, and am using ssh-agent
        effectively at work now to bounce around servers and transfer groups of
        files between servers (often by piping tar commands with ssh).
    </li>
    <li>
        A script called 'movein.sh' turned my life around when it came to
        working on the servers. I now have a .skel directory on my work machine
        that contains links to oft-used configuration files and directories, as
        well as to my ~/bin directory; this allows me to then type 'movein.sh
        server' and have all these files uploaded to the server. I can now use
        vim, screen, and other programs on any system we have in exactly the
        manner I expect to.
    </li>
    <li>
        I've started thinking about versioning more, and have plans to put into
        place a subversion repository to store server configs, database schema,
        and development projects so we won't make as many mistakes in the future
        -- at least not ones we can't rollback from.
    </li>
    <li>
        I rewrote a shell script in perl that was originally intended for IP
        takeover, and have been utilizing it to determine if and/or when a
        server we've reinstalled goes down.
    </li>
    <li>
        A bunch of Apache and MySQL tips are included, including mod_rewrite
        hacks, how to make your directory indexes show full file names, and
        more; as well as how to monitor your mysql processes and, if necessary,
        kill them. I'm also very interested in how to use MySQL as an
        authentication backend for an FTP daemon -- it could give us very
        fine-grained control of our webserver for editors.
    </li>
</ul>
<p>
    And that's just the tip of the iceberg. All in all, I highly recommend the
    book -- though most likely as a book to check out from the library for a few
    weeks, digest, put into practice, and return. The hacks are so damn useful,
    I've found that after using one, I don't need to refer to that one ever
    again. But that's the point.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;