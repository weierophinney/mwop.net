<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('124-coLinux-Recovery');
$entry->setTitle('coLinux Recovery');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1159219620);
$entry->setUpdated(1159219620);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'linux',
));

$body =<<<'EOT'
<p>
    As <a href="http://weierophinney.net/matthew/archives/103-XP-+-Cygwin-+-coLinux-Productivity.html">I've written previously,</a>
    I use <a href="http://www.colinux.org">coLinux</a> in order to have a
    Linux virtual machine running on my Windows XP install. It runs Debian
    unstable (SID), which gives me all apt-geet love I could want.
</p>
<p>
    Except when an apt-get based install goes bad, that is, like it did Saturday
    evening. This is the tale of how I got it back up and running.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    First off, I want to note that the narrative below shows the final,
    <em>successful</em> steps I took that got me back up and running. I actually
    had a number of failed attempts, but, like a scientist, kept changing one
    variable until I got a success. The below may or may not work for you, but
    it did work for me.
</p>
<p>
    Now, to the incident: I'd been installing a few updates on my machine,
    including updates to mutt and some related programs. All of a sudden, my
    machine locked up, and I knew it was an irrecoverable lockup once the hard
    drive light ceased all activity and the clock failed to show any updates.
</p>
<p>
    After reboot, my coLinux daemon silently failed on startup, and I couldn't
    determine if it was failing to start, or crashing after it booted. It took
    me a while to figure out how to run it from the command line, but that
    helped me diagnose the issue. To run the coLinux service from the command
    line, <kbd>cd</kbd> into the directory containing your coLinux executables,
    and then run <kbd>colinux-daemon.exe -c yourConfig.xml</kbd> (where
    yourConfig.xml is the name of your configuration file; best is to use the
    full <em>Windows</em> (not Cygwin) path to the configuration file).
</p>
<p>
    Unfortunately, what I was getting was a kernel panic. I decided I needed to
    go into single user mode to try and diagnose the issue. Googling told me
    that I needed to add a trailing '1' to the bootparams directive in my
    coLinux configuration file:
</p>
<pre>
&lt;bootparams&gt;root=/dev/cobd0 1&lt;/bootparams&gt;
</pre>
<p>
    Unfortunately, the kernel panic was occurring prior to the init phase --
    apparently, it was having issues with the journaling on the ext3 partition.
</p>
<p>
    So, I was stuck. And then it hit me: if I could boot into a different
    coLinux install, I could add an additional block device with the root
    partition of my own, and then do some disk analysis. Fortunately, I had the
    original Debian image I'd downloaded to use with coLinux, so I started
    experimenting.
</p>
<p>
    Sure enough, I was able to grab my partition, run an <kbd>e2fsck</kbd> on
    it, and even use <kbd>tune2fs</kbd> to remove and restore the journaling.
    The partition mounted fine and I was able to peruse the data without an
    issue.
</p>
<p>
    But I still couldn't boot it, which left me in a bit of a situation: all my
    current work is on that machine, and I have my dual-apache setup on there
    (for PHP 4 and PHP 5). I needed to be able to boot it.
</p>
<p>
    The first step was to create a new 10GB partition with a working Debian
    install on it. I copied the working Debian install (which is &lt; 2GB), and
    found a utility called <a href="http://csemler.com/">toporesize</a> that
    could resize the partition to my desired 10GB. The process takes a fair
    amount of time, and, because it's disk and CPU intensive, heats up the
    laptop something awful, so I started it before bed and set aside the
    machine.
</p>
<p>
    In the morning, I changed my coLinux config file to boot this image -- and
    it worked flawlessly. A quick <kbd>df -l</kbd> showed that the partition had
    indeed been resized. Now it was time to test apt-get to install those
    programs I'd been trying to update. All went perfectly.
</p>
<p>
    I quit the session, added a block device for my old coLinux install to the
    coLinux configuration, and restarted the virtual machine. The device was
    found, and I mounted it locally so I could start rsyncing. I needed to rsync
    my /home and /usr/local trees, as well as some key files in my /etc tree
    (samba configuration, resolve.conf files, hosts file, and a custom apache
    initscript). Again, this was a time and CPU intensive operation; however, it
    was now morning, and time to be working, so I limited my activities to
    checking email while I waited.
</p>
<p>
    The end result is that I have a shiny new install with all my tools, and,
    better yet, all my working data. Better yet, I now better understand how
    coLinux works, and know I can recover fairly quickly and effectively from
    failures in the future.
</p>
EOT;
$entry->setExtended($extended);

return $entry;