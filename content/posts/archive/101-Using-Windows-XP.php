<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('101-Using-Windows-XP');
$entry->setTitle('Using Windows XP');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1134253080);
$entry->setUpdated(1134298320);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
));

$body =<<<'EOT'
<p>
    Those of you who know me well know that I've been using Linux as my primary
    OS for many years now. At this point, for me, using Windows is like deciding
    I'm going to use a limited vocabulary; I can get the idea across, but not
    quite as well.
</p>
<p>
    Due to the nature of where I work and the fact that I'm telecommuting, I've
    been having to maintain a dual-boot system. I use <a href="http://www.ubuntu.com/">Ubuntu</a> for my daily OS, and boot into
    Windows when I need to interact with people at work via <a href="http://www.webex.com/">Webex</a> or 
    <a href="http://www.skype.com/">Skype</a> (we're using the new Skype beta
    with video, and it only works on Windows XP at this time).
</p>
<p>
    This week, however, I've had to stay on Windows quite a bit -- lots of
    impromptu conference calls and such. So, I've been customizing my
    environment, and been pretty pleased with the results.
</p>
<p>
    Continue reading for some tips on customizing your Windows XP environment to
    work and feel a little more like... linux.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    First off, one word: <a href="http://www.cygwin.com/">Cygwin</a>. Cygwin
    provides a GNU layer for Windows, including the Bash shell, and X if you
    need it. Of particular use are the inclusion of developer and sysadmin tools
    like SSH, CVS, rsync, wget, and more. This is invaluable and a must have for
    any developer.
</p>
<p>
    Speaking of developer tools, I'm a <a href="http://www.vim.org/">Vim</a>
    geek, and the next thing I did was download VIM for Windows. Sure, I can use
    vim via Cygwin, but I love having gvim available and easily launced via the
    <a href="http://mozex.mozdev.org">moxex plugin for Firefox</a>, so the
    Windows version is nice to have. And, since I like to use <b>perldo</b> with
    vim, I donwloaded <a href="http://www.activestate.com/Products/ActivePerl/">ActiveState Perl</a>.
</p>
<p>
    The next step in my oddessey was to get my GUI environment working like I
    have it in Linux. My goals:
</p>
<ul>
    <li><b>Sloppy Focus</b>. I like to be able to move my mouse over a window
    and have that window raise -- instead of clicking into it. I was able to
    achieve this via <a href="http://www.microsoft.com/windowsxp/downloads/powertoys/xppowertoys.mspx">Microsoft's TweakUI PowerTools</a>.</li>
    <li><b>Multiple workspaces</b>. If you don't know what multiple workspaces
    are, or haven't used them, you're missing out. If you find your workspace
    getting cluttered with windows, you should be using multiple workspaces. I
    group related windows in workspaces, and switch workspaces as I work in
    different projects. As an example, I might have one workspace for one
    project, another for another, another for using GIMP, etc. I achieved this
    functionality with <a href="http://virt-dimension.sourceforge.net/">Virtual Dimension</a>.</li>
    <li><b>Multiple panels/panel with launchers</b>. On my Linux desktop, I have
    a top panel that includes the equivalent of a start menu, a tasklist, and
    the system tray. A right panel autohides and has launchers for often used
    programs. Windows only allows a single taskbar (equivalent of a panel), so
    this threw me into a quandary. Then I stumbled upon <a href="http://www.objectdock.com/">ObjectDock</a>. 
    I now have an auto-hiding dock on the right side of my screen with launchers
    for my favorite programs</li>
    <li><b>Ability to shade windows</b>. Shading windows means that the window
    rolls up into just the title bar. This is useful for saving screen real
    estate, as well as 'hiding' windows that are not currently in use, while
    keeping a visual cue that the window is available on the desktop. I found 
    <a href="http://www.hmmn.org/FreeShade/">FreeShade</a> for this
    functionality; now a double-click on the title bar rolls up the window.</li>
    <li><b>Ability to maximize vertically or horizontally</b>. I rarely maximize
    a window, but often like to maximize a window vertically (particularly gvim
    or shell windows). Again, <a href="http://www.hmmn.org/FreeShade/">FreeShade</a> 
    came to the rescue; right-click on the title bar gives a menu that includes
    these options.</li>
    <li><b>Switch Ctrl and Caps Lock</b>. I find I use Ctrl often, and Caps Lock
    almost never; I've never understood why Caps Lock is in a position that's so
    easy to hit, while Ctrl is next to impossible to hit when your fingers are
    in standard typing position. In Linux, it's trivial to switch the Ctrl and
    Caps Lock keys, and I've done so for many years. I discovered how to switch
    them <a href="http://www.pitt.edu/~kconover/keithbet.htm"> via an obscure
        site</a> that covered both using a Win95 kernel toy patch as well as
    making the change directly in the registry. I went with the Win95 Kernel
    Toys, which, surprising, worked fine in WinXP, and added a 'Remap' tab to
    the Keyboard Properties capplet.</li>
</ul>
<p>
    I did a number of other tweaks. TweakUI, for instance, allows you to
    determine when windows are grouped in the taskbar. The system tray
    preferences let you choose when system tray items are hidden or displayed. 
</p>
<p>
    One thing I always need is a good music player. On Linux, I use 
    <a href="http://www.musicpd.org/">mpd</a> with 
    <a href="http://pympd.sourceforge.net/">the pympd client</a>. I've found on
    Windows that <a href="http://www.apple.com/itunes/">iTunes</a> is fantastic,
    and incredibly fast at ripping CDs to mp3 (and plays them simultaneously!).
</p>
<p>
    There are several things more I want to try. For instance, I'd like to get
    <a href="http://www.postfix.org/">Postfix</a> for Cygwin working, so I don't
    have to rely on an SMTP server; this will mean getting init for Cygwin
    working, too. I also want to figure out how to compile PHP to work under
    Cygwin at some point, so that I can have PHP installs that I've tailored for
    my needs.
</p>
<p>
    A post like this isn't complete without the requisite screenshot:
</p>
<img width='640' height='480' style="border: 0px; padding-left: 5px; padding-right: 5px;" src="/uploads/screenshot.jpg" alt="" />
<p>
    All in all, I am reluctantly finding my way around Windows, and the
    experience isn't too bad. Now, to get that 
    <a href="http://www.colinux.org/">coLinux</a> install going... but that's a
    subject for another post.
</p>
EOT;
$entry->setExtended($extended);

return $entry;