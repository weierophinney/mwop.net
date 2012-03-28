<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('24-Fun-with-Find');
$entry->setTitle('Fun with Find');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1075920036);
$entry->setUpdated(1095701924);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    I've had occasion to need to grab a specific set of files from a large
    directory -- most recently, I needed to grab some specific access logs from
    our Apache logfiles at work.
</p>
<p>
    Enter <kbd>find</kbd>.
</p>
<p>
    I needed to get all files newer than a specific date, and with the pattern
    'sitename-access_log.timestamp.gz'. I then needed to tar up these files and
    grab them for processing. So, here's what I did:
</p>
<ul>
    <li>The <kbd>-newer filename</kbd> tells find to locate files newer than
        filename.
    </li>
    <li>The <kbd>-regex</kbd> flag tells find to locate files matching the
        regular expression. The regex that find uses is a little strange,
        however, and didn't follow many conventions I know; for one thing, it's
        assumed that the pattern you write will match against the entire string,
        and not just a portion of it. What I ended up using was 
        <kbd>-regex '.*access_log.*gz'</kbd>, and that worked.
    </li>
    <li>The <kbd>-printf</kbd> flag tells find to format the printing. This is
        useful when using the output of find in another program. For instance,
        tar likes a list of filenames... so I used <kbd>printf "%p "</kbd>,
        which separated each filename with a space.
    </li>
</ul>
<p>
    I then backticked my full find statement and used it as the final argument
    to a tar command; voila! instant tar file with the files I need!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;