<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('136-Back-on-Linux-Again');
$entry->setTitle('Back on Linux Again');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1171738258);
$entry->setUpdated(1179414186);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'linux',
));

$body =<<<'EOT'
<p>
    A little over a year ago, <a href="http://weierophinney.net/matthew/archives/101-Using-Windows-XP.html">I stopped using Linux as my primary desktop</a> 
    due to the fact that a number of programs we were using were Windows
    dependent. Despite 
    <a href="http://weierophinney.net/matthew/archives/103-XP-+-Cygwin-+-coLinux-Productivity.html">getting coLinux running</a>,
    I've never been completely satisfied with the setup. I missed being able to
    paste with my middle-mouse button, and I was constantly having character
    encoding issues pasting back and forth between PuTTY and windows apps,
    couldn't access mail easily between my coLinux and Windows partitions, and
    overall felt that I was losing out on some productivity by not having a
    native linux environment as my primary OS.
</p>
<p>
    Last week, we had an infrastructure change at work, and I basically realized
    that my Windows + coLinux setup was going to get in the way of productivity
    -- and that, at this point, there were now Windows applications tying me to
    that OS. So, I decided it was time to go back to Linux.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I'd used Linux as my primary OS for five years prior to starting at Zend,
    and have used a number of distros: SuSE, Mandrake (back when it was still
    Mandrake), RedHat, Slackware, Debian, Gentoo, and Ubuntu have all been on
    my machines at one point or another. I like Gentoo quite a lot, but it's a
    pain on desktops, where you may be needing to upgrade more often than once
    every few months or years. Debian-based distros are my favorite for desktop
    machines, as the packaging mechanism is first-rate, and they tend to have
    plenty of developer packages available. So, I decided to use Ubuntu again,
    as I've heard great things about their installer since I last used it. I
    chose the latest stable release, Edgy Eft; since Feisty Fawn is still
    incubating, I didn't want to risk instability in my day-to-day work
    environment as people finalize packages. On the other hand, I also wanted a
    reasonably up-to-date system. Due to Ubuntu's dedication to a regular
    release cycle, I figured that with Edgy Eft, I'd get the best of both
    worlds.
</p>
<p>
    I was not disappointed. I had a working desktop installed in someting like
    30 minutes, with a single reboot -- and that was simply to go from the live
    CD into the actual installed OS. Even better: the initial install recognized
    <em>all</em> of my hardware immediately, including the built-in wireless
    hardware, something I've never experienced before with any Linux OS.
</p>
<p>
    That said, there were three minor issues I needed to correct:
</p>
<ul>
    <li>I was having issues compiling anything; every configure script I ran
    ended up reporting shell escaping issues or other related errors.</li>
    <li>I have an IBM T43 Thinkpad laptop with a 'Trackpoint' mouse -- basically
    a little joystick between the 'G' and 'H' keys that has mouse buttons just
    below the space bar. On Windows, you can use the middle mouse button as a
    wheel lock, allowing you to scroll with the mouse. This functionality was
    not enabled in linux, and I wasn't sure how to enable it.</li>
    <li>Suspend and hibernate were acting, well, funky. Basically, recovery
    never occurred completely, and I'd usually have no wireless access after
    resuming.</li>
    <li>Unable to mute the microphone</li>
</ul>
<p>
    I was able to find solutions for all three relatively easily, fortunately,
    and I'm sharing the solutions below:
</p>

<h3>Compilation issues: shell escaping</h3>
<p>
    I always compile apache and PHP by hand, as I can then control what I get
    precisely. This is important as it allows me to have multiple versions of
    PHP if I need them, each tuned to a different server. But as I tried to run
    the configure script for either, I was getting an error indicating that sed
    was not behaving properly due to the shell not escaping characters properly.
</p>
<p>
    I did some research, and discovered some posts claiming that the version of
    bash shipped with Ubuntu had a bug, and that the only recourse was upgrading
    bash. Of course, there was not a new version in the repository, and I
    couldn't compile a newer version due to the shell issues.
</p>
<p>
    So, I decided to see if there were another shell I could symlink /bin/sh to
    so I could recompile bash. And that's where the problem lay: /bin/sh was
    symlinking to /bin/dash -- a stripped down shell used by debian and ubuntu.
    When I symlinked it to /bin/bash instead, all the errors went away.
</p>
<p>
    Summary: relink /bin/sh to /bin/bash if you need to compile programs on
    Ubuntu.
</p>

<h3>Trackpoint usage</h3>
<p>
    I found several sites dedicated to laptops on linux, and one dedicated to
    thinkpads running linux. They were each suggesting that I'd need to (a)
    recompile X.org, and/or (b) add a kernel level driver. Once those were done,
    I'd be able to add some directives to my X configuration in order to have my
    wheel button enabled.
</p>
<p>
    I'm not sure where I found the directives, but I decided to simply try them
    and restart X. Here they are:
</p>
<pre>
# In /etc/X11/xorg.conf:
Section "InputDevice"
    Identifier  "Configured Mouse"
    Driver      "mouse"
    Option      "CorePointer"
    Option      "Device"               "/dev/input/mice"
    Option      "Protocol"             "ExplorerPS/2"
    Option      "ZAxisMapping"         "4 5"
    Option      "EmulateWheel"         "on"
    Option      "EmulateWheelButton"   "2"
    Option      "EmulateWheelInertia"  "50"
    Option      "EmulateWheelTimeOut"  "200"
    Option      "EmulateWheelClickToo" "true"
    Option      "YAxisMapping"         "4 5"
    Option      "XAxisMapping"         "6 7"
EndSection
</pre>
<p>
    With these settings in place, restart your X server (Ctrl-Alt-Backspace),
    and you're in business. I found that this actually provided <em>better</em>
    functionality than on Windows; I now can do all of:
</p>
<ul>
    <li>Use button two as a wheel lock, allowing vertical scrolling<li>
    <li>and also do <em>horizontal</em> scrolling (never could do that in
    Windows)</li>
    <li><em>and</em> use it as a middle mouse button, allowing me to paste in
    X</li>
</ul>

<h3>Suspend and Hibernate</h3>
<p>
    As I said earlier, each of these caused bizarre issues when I'd resume. The
    little 'sleep' indicator would blink continually, the wireless adapter would
    never work, and, if I tried to connect my vpn client, my machine would
    freeze. And suspend never truly suspended the machine; it would turn off the
    display, but the machine was still fully powered. It was a nightmare.
</p>
<p>
    I remembered trying suspend2 last year, but with mixed results. I decided to
    look into it some more, to see if te project has matured. I was disappointed
    at first, because it looked like I'd need to build my own kernel, and I
    didn't want to do anything that would potentially impinge on my ability to
    work. And then I saw mention of a 'hibernate' program.
</p>
<p>
    I did a quick search of the apt repositories, and saw that it was available:
</p>
<pre>
$ apt-cache search hibernate
$ apt-get install hibernate
</pre>
<p>
    Once installed, I still had some configuration to do. GNOME has some
    utilities for battery/power management that can initiate suspend and
    hibernate, and GDM of course has them as well. However, they interact with
    the acpid process. I needed to figure out how to get acpid to work with
    hibernate.
</p>
<p>
    This turned out to be pretty easy. First, replace /etc/acpi/sleep.sh with
    the following:
</p>
<pre>
#!/bin/sh
/usr/sbin/hibernate --config-file=/etc/hibernate/ram.conf
</pre>
<p>
    The, replace /etc/acpi/hibernate.sh and /etc/acpi/powerbtn.sh with:
</p>
<pre>
#!/bin/bash
# Skip if we're in the middle of resuming
test -f /var/lock/acpisleep && exit 0

/usr/sbin/hibernate --config-file=/etc/hibernate/disk.conf
</pre>
<p>
    This was only half the issue, however; while I was truly suspending and
    hibernating, and resuming, the wireless adapter was still not coming up by
    itself.
</p>
<p>
    hibernate comes with a number of configuration files. One is called
    <kbd>blacklisted-modules</kbd>. I placed the following in it:
</p>
<pre>
ath_pci
wlan_scan_sta
wlan
</pre>
<p>
    I needed to add some additional directies to /etc/hibernate/common.conf to
    make this work, and to bring the adapter down and up:
</p>
<pre>
# Unload and load modules from the blacklist. These were already set.
UnloadBlacklistedModules yes
LoadModules auto

# Bring down and restart networking:
DownInterfaces ath0
UpInterfaces ath0
</pre>
<p>
    This solved the issue of the wireless adapter very nicely.
</p>
<p>
    There were also a few other /etc/hibernate/common.conf directives I changed
    due to my machine's configuration:
</p>
<pre>
# This is an IBM laptop, so turn this on:
IbmAcpi yes

# I use GNOME; lock the screen on resume:
LockGnomeScreenSaver yes

# I don't use vbe in my X configuration, so I turned these off:
EnableVbetool no

# And I wanted to display some messages:
Xstatus gnome
XSuspendText Preparing to suspend...
XResumeText Resuming from suspend...
</pre>
<p>
    One last issue remained. You may recall discussion of a VPN client earlier.
    Well, I discovered that when I tried to fire it up after resuming from
    suspend or hibernate, my machine would lock up. The way the VPN client works
    is that a daemon is run at machine startup that loads a kernel module;
    evidently, resuming from suspend caused some sort of issue with this.
    Fortunately, hibernate has some directives for this, and I added this to
    /etc/hibernate/common.conf:
</p>
<pre>
StopServices vpnclient_init
StartServices vpnclient_init
</pre>
<p>
    StopServices stops a service located in /etc/init.d/ just prior to a suspend
    or hibernate process; StartServices does the opposite. With these directives
    in place, everything worked perfectly for me.
</p>
<h3>Muting the microphone</h3>
<p>
    I use Skype regularly, and typically in meetings will mute the microphone
    when others speak. I couldn't find a way to do this easily at first.
</p>
<p>
    The solution is that  you need to enable some extra properties of the volume
    control.
</p>
<p>
    First off, right click on the volume control applet in the system tray, and
    select 'Open Volume Control'. Then select the 'Edit' menu item, and
    'Preferences' under it. You want to select 'Capture' and 'Microphone
    Capture'. After these are selected, close the dialog.
</p>
<p>
    You'll now have a 'Capture' tab in the volume control applet. It shows a
    pair of sliders marked 'Capture'; below it are some icons, one of which is a
    microphone. To mute the microphone, click it; a red 'X' over it shows that
    it's muted. Clicking it again unmutes the microphone.
</p>
<h3>Summary</h3>
<p>
    I now have all the functionality of Windows, and then some. Plugging in my
    flash drive loads it onto the desktop immediately, and also opens up a
    Nautilus window with it. I have a full range of unix utilities available to
    use for all of my documents. I've installed beagle, and have, arguably,
    better desktop search than when using Google Desktop. Xbindkeys allows me to
    create hot keys for launching common apps. I can use Gvim if I want to,
    instead of vim, and still have access to a shell within it. The
    mail-notification applet allows me to query my local maildir store as well
    as gmail. I can use zenity with atd to create alarms for myself.
</p>
<p>
    And apps run much faster, I'm finding. I've been plaing with Zend Studio,
    which runs on Java, and it runs much more quickly than the Windows version
    ever ran for me -- meaning I may actually give it more than a cursory try.
</p>
<p>
    It's good to be back on linux!
</p>
EOT;
$entry->setExtended($extended);

return $entry;