<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('115-Cgiapp2-2.0.0-FINAL-Released');
$entry->setTitle('Cgiapp2 2.0.0 FINAL Released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1149474214);
$entry->setUpdated(1149476675);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
After several months of testing and some additional work, I've finally released the first stable version of <a href="http://cgiapp.sourceforge.net/">Cgiapp2.</a>
</p>
<p>
It is available at both the Cgiapp site as well as via <a href="/phly/index.php/package=Cgiapp2">my Phly PEAR channel</a>.</p>
<p>There were a ton of changes while creating the Cgiapp2 branch. From the release notes:</p>
<pre>
The 2.x series of Cgiapp completes a PHP5 port of Cgiapp2. PHP5 compatibility
changes include visibility operators for all properties and methods, declaration
of many methods as static and/or final, and the use of exceptions for catching
run mode errors. Most notably, though, is the fact that Cgiapp2 is now an
abstract class, with one abstract method, setup(); this enforces the fact that
you must subclass Cgiapp2 in order to create your application.

New features include:
* Callback hook system. Cgiapp2 is now an observer subject, and has hooks at
  several locations within the application. Additionally, it provides a method
  for registering new hooks in your applications. The callback hook system
  replaces the plugin system introduced in Cgiapp 1.7.0.

* Template engines are now relegated to plugin classes, and should implement the
  Cgiapp2_Plugin_Template_Interface. Shipped template engines include Smarty,
  Savant2, Savant3, and XSLT.

* Improved and more extensive error handling, which has been expanded to
  exceptions as well. Cgiapp2_Exception and Cgiapp2_Error are both observable
  subjects, with interface classes for implementing observers. This allows the
  developer to tie into exceptions and errors and perform actions when triggered
  (Log and Mail observers are implemented for each).

* Cgiapp2_FrontController class. This is a simple front controller that
  dispatches to public static methods in registered classes. Included is a
  'page' controller for handling static pages.
</pre>
<p>
I have included <a href="http://cgiapp.sourceforge.net/cgiapp2_doc/Cgiapp2/tutorial_Cgiapp28.cls.html">migration notes</a>, for those migrating from the 1.x series of Cgiapp; there is very little that you need to do, but some PHP5  changes necessitate some compatability breakages, and the new callback hook architecture and the ability to separate the template engines into plugins introduced some slight changes as well.
</p>
<p>
In testing the release, I have been writing some apps that take advantage of some of the new features, and I will be writing some tutorials in the coming weeks.</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;