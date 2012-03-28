<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('23-conditional-use-in-perl');
$entry->setTitle('conditional use in perl');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1075669417);
$entry->setUpdated(1095701677);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'perl',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    I've been struggling with how to use modules at runtime instead of compile
    time (I even wrote about this once before). I finally figured it out:
</p>
<pre>my $module = "ROX::Filer";
eval "use $module";
die "couldn't load module : $!n" if ($@);
</pre>
<p>
    Now I just need to figure out how to create objects from dynamic module
    names...!
</p>
<p>
    <b>Update:</b> Creating objects from dynamic names is as easy as dynamically
    loading the module at run-time:
</p>
<pre>my $obj = $module->new();
</pre>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;