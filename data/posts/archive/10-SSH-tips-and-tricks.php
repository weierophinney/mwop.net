<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('10-SSH-tips-and-tricks');
$entry->setTitle('SSH tips and tricks');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074733724);
$entry->setUpdated(1095131309);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
));

$body =<<<'EOT'
<p>
    In trying to implement some of the hacks in <em>Linux Server Hacks</em>, I
    had to go to the ssh manpage, where I discovered a number of cool tricks. 
</p>
<ol>
    <li>In order to get key-based authentication (i.e., passwordless) working,
    the <tt>$HOME/.ssh</tt> directory must be mode <tt>0700</tt>, and all files
    in it must be mode <tt>0600</tt>. Once that's setup properly, key-based
    authentication works perfectly.</li>
    <li>You can have a file called <em>config</em> in your <tt>$HOME/.ssh</tt>
    directory that specifies user-specific settings for using SSH, as well as a
    number of <em>host</em>-specific settings:
        <ul>
            <li><tt>Compression yes</tt> turns
            on compression</li>
            <li><tt>ForwardX11 yes</tt> turns
            on X11 forwarding by default</li>
            <li><tt>ForwardAgent yes</tt> turns
            on ssh-agent forwarding by default</li>
            <li><em>Host</em>-based settings go from one <em>Host</em> keyword
            to the next, so place them at the end of the file. Do it in the
            following order:
            <pre>
    Host nickname
    HostName actual.host.name
    User username_on_that_host
    Port PortToUse
            </pre>
            This means, for instance, that I can ssh back and forth between
            home using the same key-based authentication and the same ssh-to
            script (<a href="#ssh-to">more below</a>) I use for work servers --
            because I don't have to specify the port or the username.
            </li>
        </ul>
    </li>
</ol>
<p><a name="ssh-to">
    I mentioned a script called <tt>ssh-to</tt>
    earlier. This is a neat little hack from the server hacks book as well.
    Basically, you have the following script in your path somewhere:
</a></p>
<pre>
    #!/bin/bash
    ssh -C `basename $0` $*
</a></pre>
<p>
    Then, elsewhere in your path, you do a bunch of <tt>ln -s /path/to/ssh-to
    /path/to/$HOSTNAME</tt>, where <tt>$HOSTNAME</tt> is the name of a host to
    which you ssh regularly; this is where specifying a host nickname in your
    <tt>$HOME/.ssh/config</tt> file can come in
    handy. Then, to ssh to any such server, you simply type <tt>$HOSTNAME</tt>
    at the command line, and you're there!
</a></p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;