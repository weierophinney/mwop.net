<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('89-server-upgrades-samba-and-permissions-oh-my');
$entry->setTitle('Server upgrades, samba, and permissions, oh my!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1124466761);
$entry->setUpdated(1124467410);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
));

$body =<<<'EOT'
<p>
    Last week, we performed a long needed upgrade to the company
    file/print/intranet server. Basically, we'd been on a Redhat 8 system, and
    there were upgrades we were wanting to perform, and couldn't due to lack of
    libraries. We could have possibly compiled from source in some occasions...
    but that would likely have shuttled us into a similar dependency hell as
    using Redhat in the first place.
</p>
<p>
    So, we decided to re-install the OS, and switch to Gentoo in the process.
    We've found that Gentoo is a great distro for servers -- it allows us to
    tailor the install to the server purpose, and simultaneously provides a
    clean upgrade path via portage. 
</p>
<p>
    Things went primarily without a hitch. We lost a few databases due to a bad
    DB backup (argh! there went the wiki!), but that was the primary extent of
    the damage.
</p>
<p>
    When investigating the sytem post-install, I discovered some connectivity
    issues with Samba. Basically, when connecting via a *nix-based machine, we
    were getting symlinks reported as being local to the connecting machine, not
    the server. This meant that symlinks on the server weren't being followed --
    which caused major issues for those connecting via FTP, Mac, or Linux.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I tried the 'follow symlinks' and 'wide links' directives, but these did
    nothing. Googling for the issue wasn't turning up anything.
</p>
<p>
    And then I stumbled on a mailing list post where a person was able to answer
    their own question, and thankfully posted it to the list: turn 'unix
    extensions' off.
</p>
<p>
    Evidently, for unix clients, "these extensions enable Samba to better serve
    UNIX CIFS clients by supporting features such as symbolic links, hard links,
    etd... These extensions require a similarly enabled client." What I found
    was that either the client machines were mal-configured, or that the above
    description was faulty. As soon as I turned that off, the *nix-based clients
    no longer reported the server symlinks as local symlinks, but simply
    followed them.
</p>
<p>
    And now I can work directly on the development server, for the first time,
    instead of using SSH. Nice side-benefit!
</p>
EOT;
$entry->setExtended($extended);

return $entry;
