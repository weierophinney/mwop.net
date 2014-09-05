<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('210-Syntax-Highlighting-for-Technical-Presentations');
$entry->setTitle('Syntax Highlighting for Technical Presentations');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1236733370);
$entry->setUpdated(1236901764);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Being a technical presenter, I've often run up against the issue of how to
    present code snippets. 
</p>
    
<p>
    The easiest route is to simply cut-and-paste into your presentation
    software. However, such code is basically unreadable: it's hard to get
    indentation correct, and the lack of syntax highlighting makes them
    difficult to read (syntax highlighting helps users understand the purpose of
    the various language constructs).
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    The first trick I tried was to take screenshots of gvim. However, this had
    distinct downsides: I typically use a dark color scheme, which makes
    contrast on projector screens poor, and the resolution of the images is such
    that the text is often too small. I can of course rectify both situations by
    changing my GUI preferences, but this leads to a need to switch back and
    forth between profiles.
</p>

<p style="text-align: center"><img src="/uploads/2009-03-10-VimExample.png" alt="Screenshot created with Vim" /></p>

<p>
    The next trick I tried was to use Zend Studio or Eclipse to create my screen
    shots. In these cases, since the editor is not my primary editor, I could
    set the font size and color schema how I desire, and this worked relatively
    well.
</p>

<p style="text-align: center"><img src="/uploads/2009-03-10-EclipseExample.png" alt="Screenshot created with Eclipse" /></p>

<p>
    Except that both options really are awful. The workflow is something like
    this:
</p>

<ol>
    <li>Write some code</li>
    <li>Take a screenshot of the application window</li>
    <li>Load said screenshot in GIMP
        <ol>
            <li>Crop to expose only the code desired</li>
            <li>Create whatever effects are desired (drop shadow, reflection,
            rounded corners, etc)</li>
        </ol>
    </li>
    <li>Insert screenshot into presentation</li>
</ol>

<p>
    And what happens when you discover a typo or an error? You have to go back
    and do it all over. Additionally, you still can't zoom in on the text if
    it's too small.
</p>

<p>
    I'd finally had enough, and decided to look for syntax highlighting plugins
    for OpenOffice.org Impress. I didn't find any. But in searching, I stumbled
    across an even better solution.
</p>

<p>
    <a href="http://www.andre-simon.de/">Highlight</a> is a syntax highlighting
    utility written in C. It can syntax highlight a couple dozen languages using
    any of a couple dozen different highlighting schemas, and, better yet,
    create a variety of output formats. One of these, RTF (Rich Text Format) can
    be directly imported into most office software, including OO.o Impress.
</p>

<p>
    The usage is pretty simple: pass in a few options including an input file,
    output file, output type, and optionally the language (it usually
    autodetects fine, though), and it does the work (there are other options you
    can specify as well, including line width, font size, and more). Even
    better, you can provide directories for the source and output files --
    allowing you to batch them. When I'm creating a presentation now, I create a
    shell script that invokes the options I want and passes in a source and
    target directory, and run it anytime I add or update examples. Within OO.o,
    I then simply go to the "Import" menu, and choose "File..." -- and it comes
    in as a native object that I can actually manipulate -- including changing
    font size, line spacing and more.
</p>

<p>
    I think the results speak for themselves:
</p>

<p style="text-align: center"><img src="/uploads/2009-03-10-Highlight.png" alt="Highlight" /></p>

<p>
    The point: make your technical presentations easier to read, and easier to
    create: syntax highlight your code examples in a readable way.
</p>
EOT;
$entry->setExtended($extended);

return $entry;