<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('218-New-Zend-Framework-project-lead');
$entry->setTitle('New Zend Framework project lead');
$entry->setAuthor('matthew');
$entry->setDraft(true);
$entry->setPublic(true);
$entry->setCreated(1239711547);
$entry->setUpdated(1239711547);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
));

$body =<<<'EOT'
<p>
    We've recently reorganized the Zend Framework team. Below is an announcement
    just sent to our fw-general mailing list.
</p>

<pre>
Greetings!

The Zend Framework team has undergone some internal reorganization
recently which will have some bearing on the project.

First, the team now reports to Zeev Suraski, Zend's CTO. Please welcome
him to the lists!

Additionally, Wil Sinclair is ending his tenure as the Zend Framework
team leader. During his time as project lead, Zend Framework has made
tremendous progress in becoming the de-facto standard for PHP
development, with several significant achievements:

  * The 1.5, 1.6, and 1.7 releases, as well as the recent 1.8 preview
    release
  * Helped support and build partnerships with the Dojo Foundation,
    Adobe, and others
  * Introduction of agile methodologies to the ZF team
  * Usage of social networking media as an additional support vector

I'd like to thank Wil for his contributions to the Zend Framework
project!

I am pleased to announce my own promotion to the position of Project
Lead. I have worked with Andi and Zeev in recent weeks to define how I
will approach this role, and have emphasized during that time the need
to address and respond to community concerns and needs. 

Following the 1.8 release, the team will be focussing on items such as
whiddling down the bug backlog, improving documentation, and writing
tutorials. Additionally, we will be outlining and scoping upcoming
releases in order to publish a public roadmap for the project. Part of
this effort will include some work to make the proposal process easier
and more transparent. The hope is that having published milestones will
help focus user contributions, both in terms of new features and
stabilizing the framework.

I look forward to working with each and every one of you, and welcome
your feedback!
</pre>

<p>
    When I began at Zend three and a half years ago, I certainly never expected
    that I'd be leading the Zend Framework project. It has been a long, strange
    journey getting here, and I'm excited to be taking the reins on the project,
    and look forward to implementing some new tools and processes to better
    facilitate community involvement. I'd like to thank the Zend Framework
    community at large for the huge amount of support and feedback you've given
    me in the past few years.
</p>

<p>
    Let's marshall on and get the 1.8 release done, and step forward into the
    future!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;