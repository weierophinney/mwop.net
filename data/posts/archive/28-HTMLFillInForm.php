<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('28-HTMLFillInForm');
$entry->setTitle('HTML::FillInForm');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1076030597);
$entry->setUpdated(1095702333);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'perl',
  2 => 'personal',
));

$body =<<<'EOT'
<p>
    The CGI::Application::ValidateRM module utilizes HTML::FillInForm to fill in
    values in the form if portions did not pass validation. Basically, it
    utilizes HTML::Parser to go through and find the elements and match them to
    values. It's used because the assumption is that you've built your form into
    an HTML::Template, and that way you don't need to put in program logic into
    the form.
</p>
<p>
    Seems another good candidate for using FillInForm would be to populate a
    form with values grabbed from a database... I should look into that as well!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;