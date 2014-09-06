<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('207-Using-JSLint');
$entry->setTitle('Using JSLint');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1235135501);
$entry->setUpdated(1235135501);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'dojo',
));

$body =<<<'EOT'
<p>
    I've been doing a fair bit of programming in 
    <a href="http://dojotoolkit.org/">Dojo</a> lately, and have on occasion run
    into either inconsistent interfaces, or interfaces that simply fail to load
    in Internet Explorer. Several people have pointed out to me some
    optimizations to make, but, being a lazy programmer, I often forget to do
    so.
</p>

<p>
    Fortunately, there's a tool for lazy developers like myself: 
    <a href="http://jslint.com">JSLint</a>. Linters are commonly used in static
    development languages so that developers can verify that their programs are
    syntactically correct prior to compilation; they basically ensure that
    you're not accidentally attempting to compile something that will never
    compile in the first place. Many dynamic languages also have them; I've had
    a key bound in vim to run the current file through PHP's linter for many
    years now. JSLint provides linting capabilities for JavaScript, as well as
    some code analysis to point you towards some best practices -- mainly geared
    for cross-browser compatability.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    JSLint looks, at first, like it needs to be a web-based tool. However, this
    is not so; there are a number of JavaScript VMs you can utilize. Dojo's
    source builds, for instance, come with a version of Apache's Rhino, a JS VM
    written in Java, and JSLint provides a script for use with Rhino.
</p>

<p>
    To get JSLint running on the command line using the Rhino shipped with Dojo,
    you'll need to download the following file:
</p>

<ul>
    <li><a href="http://jslint.com/rhino/jslint.js">jslint.js</a></li>
</ul>

<p>
    Put these files in a directory of your choosing. Then, create a file called
    "jslint", with the following:
</p>

<div class="example"><pre><code lang="sh">
#!/bin/sh
exec java \
-jar /path/to/dojo/util/shrinksafe/custom_rhino.jar \
/path/to/jslint.js $1
</code></pre></div>

<p>
    Note: you'll need to put in the correct paths to your Dojo installation as
    well as to where you placed the jslint.js file.
</p>

<p>
    Make that file executable, and put it somewhere on your path. Once you do,
    you can invoke it quite simply:
</p>

<pre>
jslint foo.js
</pre>

<p>
    and get some nice output. Something I will often do is to grab all JS files
    in a tree using globbing, and then pass them individually to the linter. In
    zsh, that might look like this:
</p>

<pre>
% for f in *.js;do jslint $f;done
</pre>

<p>
    I found in most cases, following the advice of the linter eliminated any
    issues in IE, as well as fixed any inconsistencies I was observing in the
    UI. Your results may vary, of course -- but it's a tremendously useful tool
    to have in your toolbox if you're a JavaScript developer.
</p>
EOT;
$entry->setExtended($extended);

return $entry;