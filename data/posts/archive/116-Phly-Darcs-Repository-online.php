<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('116-Phly-Darcs-Repository-online');
$entry->setTitle('Phly Darcs Repository online');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1149558589);
$entry->setUpdated(1149563109);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    A <a href="http://weierophinney.net/phly/darcs/">darcs repository browser</a> is now online for the
    <a href="http://weierophinney.net/phly/">Phly channel</a>.
</p>
<p>
    If you're not familiar with <a href="http://abridgegame.org/darcs/">darcs</a>, 
    it's a revision control system, similar to <a href="http://www.gnu.org/software/gnu-arch/">GNU Arch</a>
    and <a href="http://git.or.cz/">git</a>; changes are kept as patch sets, and
    repositories are branched simply by checking them out. This makes darcs
    repositories very flexible, and incredibly easy to implement. Static
    binaries are available for most systems, which makes it easy to install on
    systems to which you have no administrator rights.
</p>
<p>
    A perl CGI script is shipped with darcs, and provides a web-based repository
    viewer. It utilizes darcs' <code>--xml-output</code> switch to create XML, which is
    then transformed using XSLT. However, there are some issues with the script;
    it is somewhat difficult to customize, and makes many assumptions about your
    system (location of configuration files, repositories, etc.). To make it
    more flexible, I ported it to PHP, using <a href="http://weierophinney.net/phly/index.php?package=Cgiapp2">Cgiapp2</a>
    and its XSLT template plugin and <a href="http://weierophinney.net/phly/index.php?package=Phly_Config">Phly_Config</a>.
</p>
<p>
    I have released this PHP darcs repository browser as <a href="http://weierophinney.net/phly/index.php?package=Phly_Darcs">Phly_Darcs</a>, 
    which contains both a Model and Controller, as well as example XSLT view
    templates. It is currently in beta as I'm still developing PHPUnit2 tests
    for some of the model functionality, as well as debating the ability to add
    write capabilities (to authenticated users only, of course).
</p>
<p><b>Update:</b> fixed links to internal pages to use absolute urls.</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;