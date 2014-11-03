<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-11-03-utopic-and-amd');
$entry->setTitle('Fixing AMD Radeon Display Issues in Ubuntu 14.10');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-11-03 14:15', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2014-11-03 14:15', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
    'ubuntu',
    'linux',
));

$body =<<<'EOT'
<p>
    After upgrading to <a href="https://wiki.ubuntu.com/UtopicUnicorn/ReleaseNotes">Ubuntu 14.10</a>,
    I faced a blank screen after boot. As in: no GUI login prompt, just a blank screen. My monitors
    were on, I'd seen the graphical splash screen as Ubuntu booted, but nothing once complete.
</p>

<p>
    Fortunately, I <em>could</em> switch over to a TTY prompt (using <kbd>Alt+F1</kbd>), so I had
    some capacity to try and fix the situation. The question was: what did I need to do?
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Go Back To Basics</h2>

<p>
    While the Linux kernel was recognizing my Radeon 6750, and even the X 
    server had no problems detecting it and setting it up, I still faced a 
    display issue. Fortunately, there's a fix for that: remove the proprietary
    drivers.
</p>

<p>
    The steps for removing the proprietary drivers are as follows:
</p>

<div class="example"><pre><code class="language-bash">
$ sudo apt-get purge 'fglrx*'
$ sudo update-alternatives --remove-all x86_64-linux-gnu_gl_conf
$ sudo apt-get install --reinstall libgl1-mesa-dri libgl1-mesa-glx
</code></pre></div>

<p>
    Some people will tell you then to reinstall the fglrx drivers Ubuntu ships, 
    or even the "fglrx-updates" set, but I found it best to go all the way back
    to basics.
</p>

<p>
    After executing the above steps, reboot so that they drivers are present in 
    the kernel.
</p>

<p>
    Once you do, you can try your luck with the proprietary drivers, using the
    "Additional Drivers" tool built into Ubuntu. I personally found that neither
    the proprietary fglrx drivers, fglrx-updates, nor the official AMD Catalyst
    sources worked -- and, after each failed attempt, I'd run the above to get
    back to a working state.
</p>

<p>
    My conclusion is that the proprietary drivers are likely not yet tested
    with the kernel sources currently in use by 14.10. Fortunately, the OSS 
    variants with which Ubuntu ships appear to be quite stable, and cover all
    the features that the proprietary versions covered previously.
</p>

<p>
    As always with a post like this: your mileage may vary. Hopefully the steps
    above will help at least a few of you; they worked for me on both my workstation
    and laptop.
</p>

EOT;
$entry->setExtended($extended);

return $entry;
