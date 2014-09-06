<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('42-Get-Firefox!');
$entry->setTitle('Get Firefox!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1096036410);
$entry->setUpdated(1096036457);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    Those who know me know that I love linux and open source. One particular
    program that firmly committed me to open source software is the Mozilla
    project -- a project that took the Netscape browser's codebase and ran with
    it to places I know I never anticipated when I first heard of the project.
</p>
<p>
    What do I like about Mozilla? Well, for starters, and most importantly,
    tabbed browsing changed the way I work. What is tabbed browsing? It's the
    ability to have multiple tabs in a browser window, allowing you to switch
    between web pages without needing to switch windows.
</p>
<p>
    Mozilla came out with a standalone browser a number of months back called,
    first Phoenix, then Firebird, and now Firefox. This standalone browser has a
    conservative number of basic features, which allow for a lean download --
    and yet, these basic features, which include tabbed browsing and disabling
    popups, far surpass Internet Explorer's features. And there are many
    extensions that you can download and integrate into the browser.
</p>
<p>
    One such extension is a tabbed browsing extension that makes tabbed browsing
    even more useful. With it, I can choose to have any links leaving a site go
    to a new tab; or have bookmarks automatically load in a new tab; or group
    tabs and save them as bookmark folders; or drag a tab to a different
    location in the tabs (allowing easy grouping).
</p>
<p>
    Frankly, there's few things I can find that Firefox can't do.
</p>
<p>
    And, on top of that, it's not integrated into the operating system. So, if
    you're on Windows, that means if you use Firefox, you're less likely to end
    up with spyware and adware -- which often is downloaded and installed by
    special IE components just by visiting sites -- ruining your internet
    experience.
</p>
<p>
    So, spread the word: Firefox is a speedy, featureful, SECURE alternative to
    Internet Explorer!
</p>
<ul>
    <li><a href="http://www.spreadfirefox.com/?q=affiliates&id=0&t=85">Get
        Firefox</a></li>
</ul>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;