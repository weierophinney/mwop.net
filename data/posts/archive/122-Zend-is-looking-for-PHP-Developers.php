<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('122-Zend-is-looking-for-PHP-Developers');
$entry->setTitle('Zend is looking for PHP Developers');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(false);
$entry->setCreated(1151715513);
$entry->setUpdated(1247758514);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'private',
  'ep_no_frontpage' => 'true',
  'ep_hiderss' => 'true',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    As regular readers of my blog know, I work for 
    <a href="http://www.zend.com">Zend Technologies, the PHP Company</a>.
    I work in the "ebiz" team, which builds applications and systems for
    supporting Zend infrastructure and websites. Normally I don't post much
    about my work there, but in this case, I'm asking for help:
</p>
<p>
    We're currently looking for a couple good PHP developers for the team.
    Skills and qualities we are looking for include:
</p>
<ul>
    <li>Knowledge and use of PHP best practices, including:<ul>
        <li>Coding standards</li>
        <li>Unit testing</li>
        <li>API and project documentation</li>
    </ul></li>
    <li>PHP4 and PHP5 knowledge</li>
    <li>Strong OOP skills</li>
    <li>Knowledge and skill working with the entire LAMP stack (you don't need
    to be a sysadmin, but you <em>do</em> need to know your way around a unix
    shell and how to install each of the AMP components in the stack)</li>
    <li>Willingness and ability to travel</li>
    <li>Ability to work in a globally distributed team</li>
</ul>
<p>
    If you feel you're qualified and are interested in a position with Zend,
    please send your resume to either matthew (at) zend.com or usjobs (at)
    zend.com; sending them to me will get more immediate attention ;-)
</p>
<p>
    I look forward to hearing from you!
</p>
<p>
    <b>Update:</b> In discussion with my superiors at Zend, this
    position will be <em>based</em> in the US office in Cupertino, CA,
    though it may occasionally require visits to other Zend offices. So,
    if you are interested in applying, please be aware that if you do
    not live in the Bay Area, you should be willing to relocate.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;