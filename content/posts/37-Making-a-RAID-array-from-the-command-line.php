<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('37-Making-a-RAID-array-from-the-command-line');
$entry->setTitle('Making a RAID array from the command line');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1079058858);
$entry->setUpdated(1095703104);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    Last night, I created my first RAID array from the commandline. It was quite
    simple, I discovered.
</p>
<ol>
    <li>Create your partitions using fstab. Remember, primary partitions must be
    created <em>before</em> extended partitions.</li>
    <li>Look in <kbd>/proc/partions</kbd> and note the new partition IDs.</li>
    <li>Edit <kbd>/etc/raidtab</kbd> and create a new RAID array. If unsure of
    the syntax, look up the <a href="http://www.tldp.org/HOWTO/Software-RAID-HOWTO.html">Linux Software
        RAID HOWTO</a> for more details.</li>
    <li>Type <kbd>mkraid /dev/md?</kbd>, where <kbd>?</kbd> is the id of the
    RAID device you just entered in <kbd>/etc/raidtab</kbd>.</li>
    <li>Format the new RAID device with your favorite filesystem, assign it a
    mount point, and start using it!</li>
</ol>
<p>
    I was impressed with how easy it was; the choices that the Anaconda
    installer present for creating a RAID array made it seem like the underlying
    process must be difficult, when in fact it may have been almost the same
    complexity if not easier.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;