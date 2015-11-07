<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('41-Cgiapp-Roadmap');
$entry->setTitle('Cgiapp Roadmap');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1095777645);
$entry->setUpdated(1095777668);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'perl',
  2 => 'personal',
  3 => 'php',
));

$body =<<<'EOT'
<p>
    I've had a few people contact me indicating interest in Cgiapp, and I've
    noticed a number of subscribers to the freshmeat project I've setup. In
    addition, we're using the library extensively at the <a href="http://www.garden.org">National Gardening Association</a> in
    developing our new site (the current site is using a mixture of ASP and
    Tango, with several newer applications using PHP). I've also been monitoring
    the CGI::Application mailing list. As a result of all this activity, I've
    decided I need to develop a roadmap for Cgiapp.
</p>
<p>Currently, planned changes include:</p>
<ul>
    <li><b>Version 1.x series:</b>
        <ul>
            <li>Adding a Smarty registration for stripslashes (the Smarty
            "function" call will be sslashes).</li>
            <li>param() bugfix: currently, calling param() with no arguments
            simply gives you a list of parameters registered with the method,
            but not their values; this will be fixed.</li>
            <li>error_mode() method. The CGI::Application ML brought up and
            implemented the idea of an error_mode() method to register
            an error_mode with the object (similar to run_modes()). While
            non-essential, it would offer a standard, built-in hook for error
            handling.</li>
            <li>$PATH_INFO traversing. Again, on the CGI::App ML, a request was
            brought up for built-in support for using $PATH_INFO to determine
            the run mode. Basically, you would pass a parameter indicating which
            location in the $PATH_INFO string holds the run mode.</li>
            <li>DocBook tutorials. I feel that too much information is given in
            the class-level documentation, and that usage tutorials need to be
            written. Since I'm documenting with PhpDoc and targetting PEAR,
            moving tutorials into DocBook is a logical step.</li>
        </ul>
    </li>
    <li><b>Version 2.x series:</b><br>
        Yes, a Cgiapp2 is in the future. There are a few changes that are either
        necessitating (a) PHP5, or (b) API changes. In keeping with PEAR
        guidelines, I'll rename the module Cgiapp2 so as not to break
        applications designed for Cgiapp.<br><br>
        Changes expected include:
        <ul>
            <li>Inherit from PEAR. This will allow for some built in error
            handling, among other things. I suspect that this will tie in with
            the error_mode(), and may also deprecate croak() and carp().</li>
            <li>Changes to tmpl_path() and load_tmpl(). In the perl version, you
            would instantiate a template using load_tmpl(), assign your
            variables to it, and then do your fetch() on it. So, this:
                <pre>
$this->tmpl_assign('var1', 'val1');
$body = $this->load_tmpl('template.html');
                </pre>
            Becomes this:
                <pre>
$tmpl = $this->load_tmpl();
$tmpl->assign('var1', 'val1');
$body = $tmpl->fetch('template.html');
                </pre>
            OR
                <pre>
$tmpl = $this->load_tmpl('template.html');
$tmpl->assign('var1', 'val1');
$body = $tmpl->fetch();
                </pre>
            (Both examples assume use of Smarty.) I want to revert to
            this behaviour for several reasons:
                <ul>
                    <li>Portability with perl. This is one area in which the PHP
                    and perl versions differ greatly; going to the perl way
                    makes porting classes between the two languages
                    simpler.</li>
                    <li>Decoupling. The current set of template methods create
                    an object as a parameter of the application object - which
                    is fine, unless the template object instantiator returns an
                    object of a different kind.</li>
                </ul>
            Cons:
                <ul>
                     <li>Smarty can use the same object to fill multiple
                     templates, and the current methods make use of this. By
                     assigning the template object locally to each method, this
                     could be lost.<br><br>
                     HOWEVER... an easy work-around would be for load_tmpl() to
                     create the object and store it an a parameter; subsequent
                     calls would return the same object reference. The
                     difficulty then would be if load_tmpl() assumed a template
                     name would be passed.  However, even in CGI::App, you
                     decide on a template engine and design for that engine;
                     there is never an assumption that template engines should
                     be swappable.</li>
                     <li>Existing Cgiapp1 applications would need to be
                     rewritten.</li>
                </ul>
            </li>
            <li><b>Plugin Architecture:</b>
                The CGI::App ML has produced a ::Plugin namespace that utilizes
                a common plugin architecture. The way it is done in perl is
                through some magic of namespaces and export routines... both of
                which are, notably, missing from PHP.<br><br>
                However, I think I may know a workaround for this, if I use
                PHP5: the magic __call() overloader method.<br><br>
                My idea is to have plugin classes register methods that should
                be accessible by a Cgiapp-based class a special key in the
                $_GLOBALS array.  Then, the __call() method would check the
                key for registered methods; if one is found matching a method
                requested, that method is called (using call_user_func()), with
                the Cgiapp-based object reference as the first reference. Voila!
                instant plugins!<br><br>
                Why do this? A library of 'standard' plugins could then be
                created, such as:
                <ul>
                    <li>A form validation plugin</li>
                    <li>Alternate template engines as plugins (instead of
                    overriding the tmpl_* methods)</li>
                    <li>An authorization plugin</li>
                </ul>
                Since the 'exported' methods would have access to the Cgiapp
                object, they could even register objects or parameters with it.
            </li>
        </ul>
    </li>
</ul>
<p>
    If you have any requests or comments on the roadmap, please feel free to <a href="/matthew/contact">contact me</a>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;