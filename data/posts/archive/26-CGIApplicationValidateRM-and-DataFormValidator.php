<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('26-CGIApplicationValidateRM-and-DataFormValidator');
$entry->setTitle('CGI::Application::ValidateRM and Data::FormValidator');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1076029663);
$entry->setUpdated(1095702081);
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
    I've been reading a lot of posts lately on the CGI::App mailing list about
    using CGI::Application::ValidateRM (RM == Run Mode); I finally went and
    checked it out.
</p>
    CGI::App::ValRM uses Data::FormValidator in order to do its magic.
    Interestingly, D::FV is built much like how I've buit our formHandlers
    library at work -- you specify a list of required fields, and a list of
    fields that need to be validated against criteria, then provide the
    criteria. It goes exactly how I would have done our libraries had we been
    working in perl -- supplying the constraint as a regexp or anonymous sub in
    a hashref for the field.

<p>
    Anyways, it looks like the combination of CGI::App::ValRM with CGI::App
    could greatly simplify any form validations I need to do on the site, which
    will in turn make me very happy!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;