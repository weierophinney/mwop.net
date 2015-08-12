<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('264-Converting-DocBook4-to-DocBook5');
$entry->setTitle('Converting DocBook4 to DocBook5');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1311112140);
$entry->setUpdated(1311290900);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
Within the <a href="https://github.com/zendframework/zf2">Zend Framework 2 repository</a>, I
recently performed a conversion from <a href="http://docbook.org/">DocBook</a> 4 to 5. The
latter is a more modern version of the toolchain, and offers a somewhat simpler
syntax and workflow. For example, you no longer need to track how many levels
deep you are in your chapter sections -- you simply use a <code>&lt;section&gt;</code> tag. One
of the nicer changes is that you do not need to specify a doctype any more; you
simply declare XML namespaces on your root elements and continue merrily on your
way. This actually allows us to remove some actions in our build process, and
makes validation much simpler.
</p>

<p>
Interestingly, for DocBook5 being available in beta since 2005 and an official
standard since 2009, there is very little material on migrating from DocBook 4
to 5. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2 id="toc_1.1">The Problems</h2>

<p>
There is a standard XSL for conversion, <code>db4-update.xsl</code>, which comes with the
DocBook5 distribution. The recommendation is simply the following:
</p>

<div class="example"><pre><code class="language-bash">
prompt&gt; xsltproc db4-update.xsl [XML File].xml &gt; [XML File].db5.xml
</code></pre></div>

<p>
Sure, this works, but I ran into a number of interesting issues.
</p>

<ul>
<li>
It actually strips out <code>&lt;![CDATA[</code> blocks, and replaces any entities
   they contain with XML entities. Typically, this is not actually what you
   want.
</li>
<li>
It injects a comment indicating that it was converted.
</li>
<li>
For some reason, the <code>db4-upgrade.xsl</code> XSLT strips out the XML declaration
   from the scripts. However, for validation purposes, and for good interop,
   it's best to retain these.
</li>
<li>
If you have defined your own entities, you may have issues in documents that
   actually consume them.
</li>
<li>
I didn't want <em>new</em> XML files, I wanted the originals replaced with my
   converted versions. I have version control, after all.
</li>
</ul>

<p>
So, to make things easier, I came up with the following approach:
</p>

<ol>
<li>
Identify files containing entity declarations, and skip them.
</li>
<li>
Replace XML entities with markers.
</li>
<li>
Run the XML file through the <code>db4-upgrade.xsl</code> stylesheet.
</li>
<li>
Restore XML entities from markers.
</li>
<li>
Strip the "conversion" comment.
</li>
<li>
Add the XML declaration if missing.
</li>
<li>
Fix <code>programlisting</code> elements (replaces entities with original text and
   wraps in <code>CDATA</code>).
</li>
<li>
Replace the original file with the converted file.
</li>
</ol>

<p>
Additionally, I wanted some robust error handling - if any given action failed,
I wanted a message indicating this, and I wanted it to stop processing so I
could fix things.
</p>

<h2 id="toc_1.2">Handling Entities</h2>

<p>
Files containing custom XML entities cause problems for the
<code>db4-upgrade.xsl</code> script. My experience is that when it encounters them, it
simply strips them out entirely, regardless of whether or not the entity file is
present. Additionally, if you've added declarations in the file for defining
where the entity definitions live, these are stripped.
</p>

<p>
The first step is making sure your entity declaration files look okay. For the
most part, these do not need to change, except for one thing: if you include a
doctype declaration, you should remove it. Make sure you note all of these files
to ensure you skip such files when processing.
</p>

<p>
The next step, and the harder by far, is converting files that <em>contain</em> those
entities in their markup. A tool by the name of <code>cloak</code> exists to make this
transformation easier, but I found that in practice, it didn't work at all -- it
instead transformed every angle bracket to an XML entity -- meaning actual XML
markup was transformed, and thus could not be converted.
</p>

<p>
Additionally, there's another problem: if you're using custom XML entities, you
actually <em>need</em> a doctype declaration that defines the location of the entities
file. As an example:
</p>

<div class="example"><pre><code class="language-xml">
&lt;!DOCTYPE table
[
    &lt;!ENTITY % language-snippets SYSTEM \&quot;./language-snippets.xml\&quot;&gt;
    %language-snippets;

    &lt;!ENTITY % language-snippets.default SYSTEM \&quot;../../en/ref/language-snippets.xml\&quot;&gt;
    %language-snippets.default;
]&gt;
</code></pre></div>

<p>
Unfortunately, the <code>db4-upgrade.xsl</code> XSLT removes these entirely. Using <code>cloak</code>
will work, but... well, you won't get anything actually converted when you're
done.
</p>

<p>
I tried to be thorough in my approach, which I'll detail below, but I'm sure
there may be some edge cases that lead to failures. The basic approach is as
follows:
</p>

<ol>
<li>
If a doctype declaration is made, strip it and move it to a separate file.
</li>
<li>
Replace any XML entities encountered with a token (basically, replace "&amp;"
   with "[amp]").
</li>
<li>
If the above have resulted in modified content, write the revised content to
   disk.
</li>
</ol>

<p>
On the flip side, after conversion of the document from DocBook4 to DocBook5,
we need to do the following:
</p>

<ol>
<li>
Replace any XML entity tokens with actual entities (basically, replace
   "[amp]" with "&amp;").
</li>
<li>
If an entities file exists for this file, inject it into the document.
</li>
<ul>
<li>
If the transformed file has an XML declaration, inject the
      doctype/entities following it.
</li>
<li>
If no XML declaration is present, simply prepend the file to the document.
</li>
</ul>
<li>
If the above have resulted in modified content, write the revised content to
   disk.
</li>
</ol>

<p>
The first script is as follows:
</p>

<div class="example"><pre><code class="language-php">
&lt;?php
// File: docbook-replace-entities.php
if ($argc &lt; 2) {
    fwrite(STDERR, \&quot;Missing file argument\n\&quot;);
    exit(1);
}

$file = $argv[1];
if (!file_exists($file)) {
    fwrite(STDERR, \&quot;Argument passed is not a file\n\&quot;);
    exit(1);
}

$xml = file_get_contents($file);

// Check if we have a doctype, and, if so, place it in a separate file and 
// strip it from this one
$transformed = preg_replace_callback(
    '#(&lt;!(DOCTYPE .*?)(]&gt;))#s', 
    function ($matches) use ($file) {
        $content = $matches[1];
        $filename = $file . '.entities';
        file_put_contents($filename, $content);
        return '';
    },  
    $xml
);

// Replace all entities with tokenized versions
$transformed = preg_replace('/\&amp;([a-zA-Z][a-zA-Z0-9._-]+;)/', '[amp]$1', $transformed);

// If no transformations have been made, exit early
if ($transformed == $xml) {
    exit(0);
}

// Write the changes back to the file
file_put_contents($file, $transformed);
</code></pre></div>

<p>
The second script, which restores the entities, looks like this:
</p>

<div class="example"><pre><code class="language-php">
&lt;?php
// File: docbook-restore-entities.php
if ($argc &lt; 2) {
    fwrite(STDERR, \&quot;Missing file argument\n\&quot;);
    exit(1);
}

$file = $argv[1];
if (!file_exists($file)) {
    fwrite(STDERR, \&quot;Argument passed is not a file\n\&quot;);
    exit(1);
}

$xml = file_get_contents($file);

// Restore tokens with actual entities
$transformed = preg_replace('/\[amp\]([a-zA-Z][a-zA-Z0-9._-]+;)/', '&amp;$1', $xml);

// Check if we have an entities file
$entitiesFile = $file . '.entities';
if (file_exists($entitiesFile)) {
    // If so, insert the entities
    $entities = file_get_contents($entitiesFile);
    if (preg_match('#^&lt;\?xml[^?]*\?&gt;#', $transformed)) {
        // If the file has an opening XML declaration, put the DOCTYPE/entities 
        // following it
        $transformed = preg_replace('#^(&lt;\?xml[^?]*\?&gt;)#', '$1' . \&quot;\n\&quot; . $entities, $transformed);
    } else {
        // Otherwise, just prepend them
        $transformed = $entities . \&quot;\n\&quot; . $transformed;
    }

    // Remove entities file when done
    unlink($entitiesFile);
}

// If no transformations have been made, we can simply exit
if ($transformed == $xml) {
    exit(0);
}

// Write changes to disk
file_put_contents($file, $transformed);
</code></pre></div>

<p>
With these ready, we can turn to the other problems.
</p>

<h2 id="toc_1.3">Helper Scripts</h2>

<p>
If you recall the original steps, several called for stripping, adding, or
transforming content after upgrading. To accomplish these tasks, I wrote several
scripts.
</p>
 
<p>
The first was one to add the XML declaration if missing (and after conversion,
most likely it is). I did this in PHP:
</p>

<div class="example"><pre><code class="language-php">
&lt;?php
// File: docbook-xml-intro.php
if ($argc &lt; 2) {
    fwrite(STDERR, \&quot;Missing file argument\n\&quot;);
    exit(1);
}

$file = $argv[1];
if (!file_exists($file)) {
    fwrite(STDERR, \&quot;Argument passed is not a file\n\&quot;);
    exit(1);
}

$xml = file_get_contents($file);
if (0 !== strpos($xml, '&lt;?xml')) {
    $xml = '&lt;?xml version=\&quot;1.0\&quot; encoding=\&quot;utf-8\&quot;?&gt;' . \&quot;\n\&quot; . $xml;
    // echo \&quot;Writing file \&quot; . $file . \&quot;\n\&quot;;
    file_put_contents($file, $xml);
}
</code></pre></div>

<p>
Second, I needed the script for fixing the <code>programlisting</code> elements. I again
did this in PHP, as this provided me with the necessary DOM tools:
</p>

<div class="example"><pre><code class="language-php">
&lt;?php
// File: docbook-programlistings.php

// DOM notices are normal; report only warnings and above
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

if ($argc &lt; 2) {
    fwrite(STDERR, \&quot;Missing file argument\n\&quot;);
    exit(1);
}

$file = $argv[1];
if (!file_exists($file)) {
    fwrite(STDERR, \&quot;Argument passed is not a file\n\&quot;);
    exit(1);
}

$doc                     = new DOMDocument();
$doc-&gt;xmlVersion         = \&quot;1.0\&quot;;
$doc-&gt;encoding           = \&quot;utf-8\&quot;;
$doc-&gt;preserveWhitespace = true;
$doc-&gt;formatOutput       = true;

if (!$doc-&gt;load($file)) {
    fwrite(STDERR, \&quot;$file: UNABLE TO LOAD FILE!\n\&quot;);
    exit(1);
}

$changed = false;
foreach ($doc-&gt;getElementsByTagName('programlisting') as $node) {
    $content = $node-&gt;textContent;
    $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
    $node-&gt;textContent = '';
    $node-&gt;nodeValue   = '';
    $cdata = $doc-&gt;createCDATASection($content);
    $node-&gt;appendChild($cdata);
    $changed = true;
}

if (!$changed) {
    // echo \&quot;$file: nothing to do\n\&quot;;
    exit(0);
}

$doc-&gt;save($file);
// echo \&quot;$file: saved\n\&quot;;
</code></pre></div>

<p>
The above worked for my particular problem -- converting ZF2 docbook -- as I
know the structure of my documentation; the approach may vary for other
projects.
</p>

<h2 id="toc_1.4">Putting it all together</h2>

<p>
Finally, it was a matter of stringing it all together. I created two scripts,
one that would convert a single file, and another that would loop through all
XML files in a given directory and invoke that script on each file.
</p>

<p>
I'll list the second one first, as it's shorter:
</p>

<div class="example"><pre><code class="language-bash">
#!/bin/bash
# File: upgradeDocbookBulk
# vim: ft=sh
XMLDIR=`pwd`
if [ \&quot;$#\&quot; -ge 1 ];then
    XMLDIR=$1
fi

echo \&quot;STARTING DOCBOOK CONVERSION\&quot;

SCRIPTDIR=`dirname $0`
ERRORS=0
for f in `find $XMLDIR -name '*.xml'`
do
    $SCRIPTDIR/upgradeDocbook $f
    if [ \&quot;$?\&quot; -ne \&quot;0\&quot; ];then
        ERRORS=1
    fi
done

echo \&quot;[DONE]\&quot;
if [ \&quot;$ERRORS\&quot; -eq \&quot;1\&quot; ];then
    echo \&quot;Script completed with errors; check logs for details.\&quot;
    exit 1
fi
</code></pre></div>

<p>
You'll note the <code>ERRORS</code> variable; basically, I'm checking to see if any single
invocation of the upgrade script results in an error; if so, I want to provide a
message at the end indicating this, and end with a non-zero exit status.
</p>

<p>
Finally, the actual upgrade script:
</p>

<div class="example"><pre><code class="language-bash">
#!/bin/bash
# File: upgradeDocbook
if [ \&quot;$#\&quot; -ne 1 ];then
    echo \&quot;USAGE: $0 &lt;xml file&gt;\&quot;
    exit 1
fi

XMLFILE=$1

# Customize the following based on your system

# Location of the db4-upgrade.xsl
UPGRADE_XSL=/usr/share/xml/docbook/stylesheet/docbook5/db4-upgrade.xsl

# Location of the PHP script for replacing entities
ENT_REPLACE_SCRIPT=`dirname $0`/docbook-replace-entities.php

# Location of the PHP script for restoring entities
ENT_RESTORE_SCRIPT=`dirname $0`/docbook-restore-entities.php

# Location of the PHP script for adding XML declarations
XML_INTRO_SCRIPT=`dirname $0`/docbook-xml-intro.php

# Location of the PHP script for processing programlistings
XML_PL_SCRIPT=`dirname $0`/docbook-programlistings.php

# Provide a space-separated list of files that should be skipped
SKIPFILES=\&quot;language-snippets.xml\&quot;

# Begin

echo \&quot;Processing $XMLFILE\&quot;

# Check if this is a skipfile
for i in $SKIPFILES;do 
    if [[ `basename \&quot;$XMLFILE\&quot;` = $i ]];then
        echo \&quot;    Skipping; file is in skip list\&quot;
        exit
    fi
done

# Copy the file to a temporary location
WORKFILE=\&quot;$XMLFILE.transform\&quot;
cp $XMLFILE $WORKFILE

# Replace entities
printf \&quot;%-64s\&quot; \&quot;    Replacing XML entities...\&quot;
php $ENT_REPLACE_SCRIPT $WORKFILE
if [ \&quot;$?\&quot; -ne \&quot;0\&quot; ];then
    printf \&quot; %7s\n\&quot; \&quot;[FAIL]\&quot;
    echo \&quot;    FAILED: Replacing XML entities in $XMLFILE\&quot; &gt;&amp;2
    exit 1
fi
printf \&quot; %7s\n\&quot; \&quot;[DONE]\&quot;

printf \&quot;%-64s\n\&quot; \&quot;    Converting from DocBook 4 to 5...\&quot;
xsltproc $UPGRADE_XSL $WORKFILE &gt; $WORKFILE.db5
if [ `stat --print=\&quot;%s\&quot; $WORKFILE.db5` -lt 200 ];then
    printf \&quot; %7s\n\&quot; \&quot;[FAIL]\&quot;
    echo \&quot;    FAILED: Conversion of $XMLFILE\&quot; &gt;&amp;2
    exit 1
fi
printf \&quot; %7s\n\&quot; \&quot;[DONE]\&quot;

# Overwrite working file with transformed content
mv $WORKFILE.db5 $WORKFILE

# Restore entities
printf \&quot;%-64s\&quot; \&quot;    Restoring XML entities...\&quot;
php $ENT_RESTORE_SCRIPT $WORKFILE
if [ \&quot;$?\&quot; -ne \&quot;0\&quot; ];then
    printf \&quot; %7s\n\&quot; \&quot;[FAIL]\&quot;
    echo \&quot;    FAILED: Restoring XML entities in $XMLFILE\&quot; &gt;&amp;2
    exit 1
fi
printf \&quot; %7s\n\&quot; \&quot;[DONE]\&quot;

printf \&quot;%-64s\&quot; \&quot;    Stripping conversion comment...\&quot;
sed --regexp-extended --in-place 's///' $WORKFILE
if [ \&quot;$?\&quot; -ne \&quot;0\&quot; ];then
    printf \&quot; %7s\n\&quot; \&quot;[FAIL]\&quot;
    echo \&quot;    FAILED: Stripping DB4 conversion comments in $XMLFILE\&quot; &gt;&amp;2
    exit 1
fi
printf \&quot; %7s\n\&quot; \&quot;[DONE]\&quot;

printf \&quot;%-64s\&quot; \&quot;    Adding XML declaration...\&quot;
php $XML_INTRO_SCRIPT $WORKFILE
if [ \&quot;$?\&quot; -ne \&quot;0\&quot; ];then
    printf \&quot; %7s\n\&quot; \&quot;[FAIL]\&quot;
    echo \&quot;    FAILED: Adding XML declaration in $XMLFILE\&quot; &gt;&amp;2
    exit 1
fi
printf \&quot; %7s\n\&quot; \&quot;[DONE]\&quot;

printf \&quot;%-64s\&quot; \&quot;    Fixing programlisting blocks...\&quot;
php $XML_PL_SCRIPT $WORKFILE 1&gt;&amp;2
if [ \&quot;$?\&quot; -ne \&quot;0\&quot; ];then
    printf \&quot; %7s\n\&quot; \&quot;[FAIL]\&quot;
    echo \&quot;    FAILED: Fixing program listings in $XMLFILE\&quot; &gt;&amp;2
    exit 1
fi
printf \&quot; %7s\n\&quot; \&quot;[DONE]\&quot;

mv $WORKFILE $XMLFILE
exit 0
</code></pre></div>

<p>
The above script satisfies the bullet points I laid out. More importantly,
between the two scripts, I can now process an entire tree of files. As an
example:
</p>

<div class="example"><pre><code class="language-bash">
prompt&gt; path/to/upgradeDocbookBulk 2&gt;&amp;1 | tee -a error.log
</code></pre></div>

<p>
I can then grep the error log for "FAIL" to see what failures I might have had:
</p>

<div class="example"><pre><code class="language-bash">
prompt&gt; grep FAIL error.log
</code></pre></div>

<p>
Because a file is not moved if a failure happens, I can then look at the
differences between the original and the converted version, and determine what
the issue may be. (Hint: in the entire ZF2 documentation tree, I only had a
handful of errors or less. In all cases, they were due to non-validating XML.)
</p>

<h2 id="toc_1.5">Conclusions</h2>

<p>
Converting from DocBook4 to DocBook5 is a non-trivial task, but, fortunately,
one that can be fairly easily automated -- assuming you know what the
<code>db4-upgrade.xsl</code> script does and does not do. Hopefully, this post sheds some
light on that, and helps describe a process you can use and/or modify to perform
a comprehensive migration.
</p>

<p>
I've put the scripts together in a repository on <a href="http://github.com">GitHub</a>:
</p>

<ul>
<li>
<a href="https://github.com/weierophinney/docbook5-migration">https://github.com/weierophinney/docbook5-migration</a>
</li>
</ul>

<p>
Feel free to clone, fork, etc.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
