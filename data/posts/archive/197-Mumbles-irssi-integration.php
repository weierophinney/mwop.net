<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('197-Mumbles-irssi-integration');
$entry->setTitle('Mumbles irssi integration');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1228939310);
$entry->setUpdated(1229113190);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'perl',
  1 => 'programming',
));

$body =<<<'EOT'
<p>
    I've been using IRC regularly for the past six to nine months, in large part
    due to the growing ZF community on the 
    <a href="http://freenode.net/">Freenode</a> #zftalk channel (unfortunately,
    I simply don't have time to be in that particular channel any more, but you
    can generally find me in #zftalk.dev), but also to keep in contact with
    other peers, friends, and colleagues. 
</p>

<p>
    One difficulty, however, is keeping productivity high while staying on IRC.
    To me, the ultimate client would provide me notifications when somebody
    mentions my name or a watch word - allowing me to read the channel at my
    leisure, yet still respond to people in a timely fashion.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I've tried a variety of IRC clients over the months, starting with Pidgin
    (poor interface for IRC), mirc (huge difficulties figuring out the UI),
    xchat (not bad, but seemed a bit heavy), Chatzilla (I liked the interface,
    but once you got many tabs going, it was unwieldy switching around between
    them; I also hated that Firefox dying or restarting caused Chatzilla to do
    likewise), and now <a href="http://irssi.org/">irssi</a>. 
</p>

<p>
    So far, irssi is the best I've tried -- I can run it in screen,
    allowing me to keep it open as long as my machine is running. The interface
    is reasonable, and cleanly keeps channels separate from private messages.
    Opening, closing, and manipulating windows is easy. And it's highly
    scriptable... including in a language I actually use! The perl bindings are
    top notch, though sometimes cryptic. What's important, however, is that
    there are plenty of examples out there if you want to try doing something.
    So, I figured I'd write up a quick plugin to trigger notifications.
</p>

<p>
    I've been using a number of different notification servers for linux, and
    personally like both <a href="http://gnotify.sourceforge.net">gnotify</a>
    and <a href="http://www.mumbles-project.org/">mumbles</a>. Both are very
    lightweight and offer network protocols for triggering notifications.
</p>

<p>
    I first tried using gnotify. It has a very, very simple TCP protocol, and
    I've had success creating messages from the shell, PHP, and perl.
    Unfortunately, for some reason, using it in irssi displayed some odd
    behavior: I'd lose the cursor and the ability to enter input from the time a
    notification triggered until it completed (i.e., the notification
    disappeared). Forking the process did not appear to help.
</p>

<p>
    So, I decided to try out mumbles. Mumbles is written in python, and has
    themeable notifications -- already a plus. It runs via dbus by default, but
    can also optionally spawn a server that implements the 
    <a href="http://growl.info/">Growl protocol</a> -- making it accessible for
    any process to send notifications. Additionally, it has a command-line
    utility for triggering notifications -- by default over dbus, but optionally
    by contacting the growl server, if running.
</p>

<p>
    Growl's protocol is a bit involved, and I didn't want to spend too much time
    on this. So, I did a quick, dirty hack: I used backticks to trigger the CLI
    utility. And it works <em>fantastically</em> -- no delays whatsoever. Here's
    the code:
</p>

<div class="example"><pre><code class="language-perl">
# $HOME/.irssi/scripts/mumbles.pl
use strict;
use Irssi;
use Irssi::Irc;
use vars qw($VERSION %IRSSI);

$VERSION = '0.1.0';
%IRSSI = (
    authors     =&gt; \&quot;Matthew Weier O'Phinney\&quot;,
    contact     =&gt; 'matthew@weierophinney.net',
    name        =&gt; 'Mumbles notifications for irssi',
    description =&gt; 'This script enables mumbles notifications for irssi',
    license     =&gt; 'New BSD',
	changed     =&gt; \&quot;2008-12-10\&quot;
);


sub mumbles_sig_printtext {
  my ($dest, $text, $stripped) = @_;

  if (($dest-&gt;{level} &amp; (MSGLEVEL_HILIGHT|MSGLEVEL_MSGS)) &amp;&amp; ($dest-&gt;{level} &amp; MSGLEVEL_NOHILIGHT) == 0)
  {
    if ($dest-&gt;{level} &amp; MSGLEVEL_PUBLIC)
    {
      mumbles($dest-&gt;{target} . \&quot; : \&quot; . $text);
    }
  }
}

sub mumbles {
    my $message = shift;
    my $response;

    $message =~ s/[^!-~\s]//g;

    `/usr/bin/mumbles-send -g 127.0.0.1 -s \&quot;IRC\&quot; \&quot;$message\&quot;`;
}

Irssi::command_bind 'mumbles' =&gt; sub {
    my ($message) = @_;
    mumbles($message);
};

Irssi::signal_add({
  'print text'    =&gt; \&amp;mumbles_sig_printtext
});
</code></pre></div>

<p>
    This triggers a notification for any "highlight" event -- basically, anytime
    anybody "says" my name in a channel, or mentions a keyword I've marked for
    highlighting. Additionally, I created a "mumbles" command so that I can send
    test messages (usage: "/mumbles "this is the message..."). You could
    certainly bind to other events, such as topic changes, joins, parts, etc --
    I'm only interested in highlight events.
</p>

<p>
    You may note the regexp in there. One thing I discovered was that most
    messages contained control and non-ascii characters that often resulted in
    unreadable notifications, as well as some nasty messages reported by irssi.
    The regexp removes anything not in the ascii character set or the set of
    whitespace definitions prior to emitting the notification.
</p>

<p>
    Something else I needed to do was configure compiz to ensure that the
    notifications actually popped <em>above</em> my windows. I did this by going
    into the compiz configuration manager, selecting "General Options",
    selecting the "Focus &amp; Raise Behaviour" tab, and modifying the "Focus
    Prevention Windows" to read as follows:
</p>

<code><pre>
(any) &amp; !(class=Mumbles)
</pre></code>

<p>
    To test it, I placed the script in $HOME/.irssi/scripts/mumbles.pl, and then, 
    in irssi, executed "/load mumbles.pl".

<p>
    Once I had it to my liking, I symlinked it into my $HOME/.irssi/scripts/autorun/
    directory, allowing it to run as soon as irssi loads. I can now have irssi 
    running in a screen session, or minimize the terminal, and get notifications 
    -- keeping me productive and informed at the same time.
</p>

<p>
    <b>UPDATED 2008-12-12:</b> Added information on how to load the script, as 
    well as fixed the location to the autorun directory. Thanks, @sidhighwind!
</p>
EOT;
$entry->setExtended($extended);

return $entry;
