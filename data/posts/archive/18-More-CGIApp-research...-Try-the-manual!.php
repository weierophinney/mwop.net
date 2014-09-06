<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('18-More-CGIApp-research...-Try-the-manual!');
$entry->setTitle('More CGI::App research... Try the manual!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074916950);
$entry->setUpdated(1095701264);
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
    So, I'm a bit of an idiot... it's been so long since I looked at CGI::App,
    and yet I felt I had such a grasp on it, that I overlooked the obvious step:
    look at the <a href="http://search.cpan.org/%7Ejerlbaum/CGI-Application-3.1/Application.pm">manual!</a>
</p>
<p>
    In particular, there's a whole series of methods that are used to tailor
    CGI:App to your particular needs, and these include <em>cgiapp_init(),
        cgiapp_prerun(), and cgiapp_postrun()</em>.
</p>
<ul>
    <li><b>cgiapp_init()</b> is used to perform application specific
        initialization behaviour, and is called immediately before the setup()
        method. It can be used to load settings from elsewhere; if it were
        called only from a superclass from which other modules inherited, it
        would then provide common settings for all modules.
    </li>
    <li><b>cgiapp_prerun()</b> is called immediately before the selected
        run-mode. If it were called only by your superclass, you could perform
        items such as authorization or even form validation; this would then be
        standard for all your applications. (You can use the
        $self->prerun_mode('mode') call to to override the selected run-mode,
        for instance, thus allowing you to redirect to a different mode if a
        user isn't permitted there.)
    </li>
    <li><b>cgiapp_postrun()</b> is called after the run-mode has returned its
        output, but before http headers have been generated or anything sent to
        the web browser. Again, if defined in a superclass, it means that you
        could then place the run-mode output <em>in a specific place within a
            larger template</em>, and even call other routines to fill in other
        parts of the main template. You could even check to see if certain
        parameters were passed to the page, and change the type of output you
        send back (XML, PDF, image, etc.), allowing you to have a common query
        element that changes the output type (e.g., a 'print' parameter that
        returns a PDF or a stripped down template).
    </li>
</ul>
<p>
    In addition, you could specify in the superclass that you're using
    CGI::Simple for the query object (using the <em>cgiapp_get_query</em>
    method), or you could rewrite the <em>load_tmpl()</em> method to use
    Template::Toolkit or some other templating system, etc.
</p>
<p>
    Doesn't look so crazy anymore...
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;