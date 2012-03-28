<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('103-XP-+-Cygwin-+-coLinux-Productivity');
$entry->setTitle('XP + Cygwin + coLinux == Productivity');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1136498220);
$entry->setUpdated(1137009503);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
  1 => 'programming',
  2 => 'linux',
));

$body =<<<'EOT'
<p>
    I wrote earlier of my experiences <a href="http://weierophinney.net/matthew/archives/101-Using-Windows-XP.html">using Windows XP</a>,
    a move I've considered somewhat unfortunate but necessary. I've added a
    couple more tools to my toolbox since that have made the environment even
    better.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    A co-worker told me about <a href="http://colinux.sourceforge.net/">coLinux</a>,
    a port of the linux kernel that allows it to run side-by-side with Windows
    on the same machine. It's kind of like vmware, only more optimized, and
    free. I'd looked at it, but was a bit daunted as I wanted to try and use my
    existing Ubuntu install with it, and was worried about messing up the
    machine.
</p>
<p>
    I finally came to the realization, however, that I simply won't be using
    linux as my day-to-day OS until some of my tools are ported. So, I blew away
    my ubuntu install and made room for coLinux.
</p>
<p>
    I'd heard that it was difficult to setup, but I found it fairly easy --
    download the coLinux tools, get a filesystem image, add the service, fire it
    up. You then need to do a few other things -- bridge your network interface
    with the coLinux network interface, set your network IP for the coLinux
    install, setup your root password and any new users you want -- but then
    it's running. You can then use <a href="http://www.cygwin.com/">Cygwin</a> 
    to SSH into the install.
</p>
<p>
    The basic coLinux filesystem is Debian, and based on an old Sid version. It
    is very stripped down, and has no developer tools. I had to apt-get a ton of
    stuff -- gcc, cpp, cvs, subversion, darcs, libtool, some development
    libraries, etc -- so I could start compiling things. I compiled Vim by hand,
    because if you want Vim with perl support in Debian, it insists on
    installing a ton of X related stuff. I then compiled Apache2, PHP4, and PHP5
    by hand (and needed to get additional development libraries for some
    features I wanted). But the compiles worked flawlessly, and I now have
    coLinux running on the machine with a flexible development environment that
    I control.
</p>
<p>
    (I've also figured out a way to run PHP4 and PHP5 seemingly on the same
    Apache install, side-by-side, but that's a topic for another day.)
</p>
<p>
    While you can access the system via SSH, I find that's not terribly
    convenient for doing simple things like editing files. So I installed Samba
    in my coLinux install, and set it up with a few shares. With that in place,
    I can now access files directly from Windows -- editing them in gVim, etc.
</p>
<p>
    I setup <a href="http://www.exim.org/">Exim</a> via cygwin. However, I
    noticed when I'd try and send emails from my coLinux install via the cygwin
    exim, exim typically errored -- usually an inability to fork a process. So I
    installed it via coLinux instead, and all is hunky dory -- my PHP scripts
    can now send mail, and I have a local SMTP server for queuing and sending
    mail instead of having to rely on the company or personal mail server. 
</p>
<p>
    In reading on the coLinux site, I discovered that you can setup programs
    that utilize esd, and run esd off of cygwin. This has allowed me to once
    again use <a href="http://musicpd.org/">mpd</a> as my preferred music 
    player.
</p>
<p>
    Since I'm constantly going into my coLinux install, I created a copy of the
    cygwin.bat script that adds a '-c "ssh myname@myCoLinuxInstall"' to the bash
    command; this allows me to click on a single icon in order to SSH into
    coLinux -- very handy.
</p>
<p>
    All-in-all, I now have what I consider to be the best of both worlds --
    access to the work programs I need, ease of configuration for a variety of
    tools (wireless, bluetooth, USB devices), and a robust server/development
    environment -- all on the same box.
</p>
EOT;
$entry->setExtended($extended);

return $entry;