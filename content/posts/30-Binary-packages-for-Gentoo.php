<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('30-Binary-packages-for-Gentoo');
$entry->setTitle('Binary packages for Gentoo');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1083120533);
$entry->setUpdated(1095702516);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    I'd read that you could get binary packages for gentoo, thus alleviating the
    need to compile everything. (Of course, then  you lose some of the benefits
    of compiling everything, but you gain in speed...) Unfortunately, I mistook
    this with ebuilds, and never quite figured it out.
</p>
<p>
    The key is to throw the -g flag:
</p>
<pre>    % emerge -g gnumeric # which is like 'emerge --getbinpkg gnumeric'
</pre>
<p>
    I also learned how to update packages tonight:
</p>
<pre>    % emerge sync             # to sync your mirror with the gentoo mirrors
    % emerge --update portage # if necessary
    % emerge --update system  # updates core system files
    % emerge --update world   # updates all packages 
</pre>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;