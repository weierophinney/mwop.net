<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('93-File_SMBPasswd-woes');
$entry->setTitle('File_SMBPasswd woes');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1126124400);
$entry->setUpdated(1126187423);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    I've been cobbling together a system at work for the last couple months to
    allow a single place for changing all network passwords. This includes a
    variety of database sources, as well as <i>passwd</i> files and
    <i>smbpasswd</i> files.
    I've been making use of PEAR's <a href="http://pear.php.net/package/File_Passwd">File_Passwd</a> and <a href="http://pear.php.net/package/File_SMBPasswd">File_SMBPasswd</a>, and they've
    greatly simplified the task of updating passwords for those types of
    systems. However, I've encountered some issues that I never would have
    expected.
</p>
<p>
    I have the web user in a group called 'samba', and I have the
    <i>smbpasswd</i> file owned by root:samba. I then set the <i>smbpasswd</i>
    file to be group +rw. Simple, right? The web user should then be able to
    update the <i>smbpasswd</i> file without a problem, right? Wrong.
</p>
<p>
    I kept getting errors, and on investigation continually found that the
    <i>smbpasswd</i> file permissions had reverted to 0600 -- i.e., only the
    root user could access it. I tried using 'chattr -i' on the off-chance that
    the file had been made immutable (which didn't make sense, as I was able to
    see the permissions change). No luck.
</p>
<p>
    Based on observations of when the permissions reverted, it appears that the
    various SMB processes will reset the permissions! An example is when
    someone attempts to mount a resource from the server; this accesses the
    smbpasswd file to perform authentication -- and at this point the file
    permissions change.  I can find no documentation to support this; these are
    simply my observations.
</p>
<p>
    So, to get around the behaviour, I created a script that will set the file
    permissions to what I want them, and then gave <i>sudo</i> privileges to the
    samba group for that script. This script is then called via <i>system()</i>
    in the update script just before processing.
</p>
<p>
    It's a hack, and could be made more secure, but it works.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;