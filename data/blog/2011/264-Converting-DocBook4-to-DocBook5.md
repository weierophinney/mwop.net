---
id: 264-Converting-DocBook4-to-DocBook5
author: matthew
title: 'Converting DocBook4 to DocBook5'
draft: false
public: true
created: '2011-07-19T17:49:00-04:00'
updated: '2011-07-21T19:28:20-04:00'
tags:
    - php
---
Within the [Zend Framework 2 repository](https://github.com/zendframework/zf2),
I recently performed a conversion from [DocBook](http://docbook.org/) 4 to 5.
The latter is a more modern version of the toolchain, and offers a somewhat
simpler syntax and workflow. For example, you no longer need to track how many
levels deep you are in your chapter sections — you simply use a `<section>` tag.
One of the nicer changes is that you do not need to specify a doctype any more;
you simply declare XML namespaces on your root elements and continue merrily on
your way. This actually allows us to remove some actions in our build process,
and makes validation much simpler.

Interestingly, for DocBook5 being available in beta since 2005 and an official
standard since 2009, there is very little material on migrating from DocBook 4
to 5.

<!--- EXTENDED -->

The Problems
------------

There is a standard XSL for conversion, `db4-update.xsl`, which comes with the
DocBook5 distribution. The recommendation is simply the following:

```bash
$ xsltproc db4-update.xsl [XML File].xml > [XML File].db5.xml
```

Sure, this works, but I ran into a number of interesting issues.

- It actually strips out `<![CDATA[` blocks, and replaces any entities they contain with XML entities. Typically, this is not actually what you want.
- It injects a comment indicating that it was converted.
- For some reason, the `db4-upgrade.xsl` XSLT strips out the XML declaration from the scripts. However, for validation purposes, and for good interop, it's best to retain these.
- If you have defined your own entities, you may have issues in documents that actually consume them.
- I didn't want *new* XML files, I wanted the originals replaced with my converted versions. I have version control, after all.

So, to make things easier, I came up with the following approach:

1. Identify files containing entity declarations, and skip them.
2. Replace XML entities with markers.
3. Run the XML file through the `db4-upgrade.xsl` stylesheet.
4. Restore XML entities from markers.
5. Strip the "conversion" comment.
6. Add the XML declaration if missing.
7. Fix `programlisting` elements (replaces entities with original text and wraps in `CDATA`).
8. Replace the original file with the converted file.

Additionally, I wanted some robust error handling — if any given action failed,
I wanted a message indicating this, and I wanted it to stop processing so I
could fix things.

Handling Entities
-----------------

Files containing custom XML entities cause problems for the `db4-upgrade.xsl`
script. My experience is that when it encounters them, it simply strips them out
entirely, regardless of whether or not the entity file is present. Additionally,
if you've added declarations in the file for defining where the entity
definitions live, these are stripped.

The first step is making sure your entity declaration files look okay. For the
most part, these do not need to change, except for one thing: if you include a
doctype declaration, you should remove it. Make sure you note all of these files
to ensure you skip such files when processing.

The next step, and the harder by far, is converting files that *contain* those
entities in their markup. A tool by the name of `cloak` exists to make this
transformation easier, but I found that in practice, it didn't work at all — it
instead transformed every angle bracket to an XML entity — meaning actual XML
markup was transformed, and thus could not be converted.

Additionally, there's another problem: if you're using custom XML entities, you
actually *need* a doctype declaration that defines the location of the entities
file. As an example:

```xml
<!DOCTYPE table
[
    <!ENTITY % language-snippets SYSTEM "./language-snippets.xml">
    %language-snippets;

    <!ENTITY % language-snippets.default SYSTEM "../../en/ref/language-snippets.xml">
    %language-snippets.default;
]>
```

Unfortunately, the `db4-upgrade.xsl` XSLT removes these entirely. Using `cloak`
will work, but… well, you won't get anything actually converted when you're done.

I tried to be thorough in my approach, which I'll detail below, but I'm sure
there may be some edge cases that lead to failures. The basic approach is as
follows:

1. If a doctype declaration is made, strip it and move it to a separate file.
2. Replace any XML entities encountered with a token (basically, replace `&` with `[amp]`).
3. If the above have resulted in modified content, write the revised content to disk.

On the flip side, after conversion of the document from DocBook4 to DocBook5, we
need to do the following:

Replace any XML entity tokens with actual entities (basically, replace `[amp]`
with `&`).

If an entities file exists for this file, inject it into the document.

- If the transformed file has an XML declaration, inject the doctype/entities following it.
- If no XML declaration is present, simply prepend the file to the document.

If the above have resulted in modified content, write the revised content to disk.

The first script is as follows:

```php
<?php
// File: docbook-replace-entities.php
if ($argc < 2) {
    fwrite(STDERR, "Missing file argument\n");
    exit(1);
}

$file = $argv[1];
if (!file_exists($file)) {
    fwrite(STDERR, "Argument passed is not a file\n");
    exit(1);
}

$xml = file_get_contents($file);

// Check if we have a doctype, and, if so, place it in a separate file and 
// strip it from this one
$transformed = preg_replace_callback(
    '#(<!(DOCTYPE .*?)(]>))#s', 
    function ($matches) use ($file) {
        $content = $matches[1];
        $filename = $file . '.entities';
        file_put_contents($filename, $content);
        return '';
    },  
    $xml
);

// Replace all entities with tokenized versions
$transformed = preg_replace('/\&([a-zA-Z][a-zA-Z0-9._-]+;)/', '[amp]$1', $transformed);

// If no transformations have been made, exit early
if ($transformed == $xml) {
    exit(0);
}

// Write the changes back to the file
file_put_contents($file, $transformed);
```

The second script, which restores the entities, looks like this:

```php
<?php
// File: docbook-restore-entities.php
if ($argc < 2) {
    fwrite(STDERR, "Missing file argument\n");
    exit(1);
}

$file = $argv[1];
if (!file_exists($file)) {
    fwrite(STDERR, "Argument passed is not a file\n");
    exit(1);
}

$xml = file_get_contents($file);

// Restore tokens with actual entities
$transformed = preg_replace('/\[amp\]([a-zA-Z][a-zA-Z0-9._-]+;)/', '&$1', $xml);

// Check if we have an entities file
$entitiesFile = $file . '.entities';
if (file_exists($entitiesFile)) {
    // If so, insert the entities
    $entities = file_get_contents($entitiesFile);
    if (preg_match('#^<\?xml[^?]*\?>#', $transformed)) {
        // If the file has an opening XML declaration, put the DOCTYPE/entities 
        // following it
        $transformed = preg_replace('#^(<\?xml[^?]*\?>)#', '$1' . "\n" . $entities, $transformed);
    } else {
        // Otherwise, just prepend them
        $transformed = $entities . "\n" . $transformed;
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
```

With these ready, we can turn to the other problems.

Helper Scripts
--------------

If you recall the original steps, several called for stripping, adding, or
transforming content after upgrading. To accomplish these tasks, I wrote several
scripts.

The first was one to add the XML declaration if missing (and after conversion,
most likely it is). I did this in PHP:

```php
<?php
// File: docbook-xml-intro.php
if ($argc < 2) {
    fwrite(STDERR, "Missing file argument\n");
    exit(1);
}

$file = $argv[1];
if (!file_exists($file)) {
    fwrite(STDERR, "Argument passed is not a file\n");
    exit(1);
}

$xml = file_get_contents($file);
if (0 !== strpos($xml, '<?xml')) {
    $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . $xml;
    // echo "Writing file " . $file . "\n";
    file_put_contents($file, $xml);
}
```

Second, I needed the script for fixing the `programlisting` elements. I again
did this in PHP, as this provided me with the necessary DOM tools:

```php
<?php
// File: docbook-programlistings.php

// DOM notices are normal; report only warnings and above
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

if ($argc < 2) {
    fwrite(STDERR, "Missing file argument\n");
    exit(1);
}

$file = $argv[1];
if (!file_exists($file)) {
    fwrite(STDERR, "Argument passed is not a file\n");
    exit(1);
}

$doc                     = new DOMDocument();
$doc->xmlVersion         = "1.0";
$doc->encoding           = "utf-8";
$doc->preserveWhitespace = true;
$doc->formatOutput       = true;

if (!$doc->load($file)) {
    fwrite(STDERR, "$file: UNABLE TO LOAD FILE!\n");
    exit(1);
}

$changed = false;
foreach ($doc->getElementsByTagName('programlisting') as $node) {
    $content = $node->textContent;
    $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
    $node->textContent = '';
    $node->nodeValue   = '';
    $cdata = $doc->createCDATASection($content);
    $node->appendChild($cdata);
    $changed = true;
}

if (!$changed) {
    // echo "$file: nothing to do\n";
    exit(0);
}

$doc->save($file);
// echo "$file: saved\n";
```

The above worked for my particular problem — converting ZF2 docbook — as I know
the structure of my documentation; the approach may vary for other projects.

Putting it all together
-----------------------

Finally, it was a matter of stringing it all together. I created two scripts,
one that would convert a single file, and another that would loop through all
XML files in a given directory and invoke that script on each file.

I'll list the second one first, as it's shorter:

```bash
#!/bin/bash
# File: upgradeDocbookBulk
# vim: ft=sh
XMLDIR=`pwd`
if [ "$#" -ge 1 ];then
    XMLDIR=$1
fi

echo "STARTING DOCBOOK CONVERSION"

SCRIPTDIR=`dirname $0`
ERRORS=0
for f in `find $XMLDIR -name '*.xml'`
do
    $SCRIPTDIR/upgradeDocbook $f
    if [ "$?" -ne "0" ];then
        ERRORS=1
    fi
done

echo "[DONE]"
if [ "$ERRORS" -eq "1" ];then
    echo "Script completed with errors; check logs for details."
    exit 1
fi
```

You'll note the `ERRORS` variable; basically, I'm checking to see if any single
invocation of the upgrade script results in an error; if so, I want to provide a
message at the end indicating this, and end with a non-zero exit status.

Finally, the actual upgrade script:

```bash
#!/bin/bash
# File: upgradeDocbook
if [ "$#" -ne 1 ];then
    echo "USAGE: $0 <xml file>"
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
SKIPFILES="language-snippets.xml"

# Begin

echo "Processing $XMLFILE"

# Check if this is a skipfile
for i in $SKIPFILES;do 
    if [[ `basename "$XMLFILE"` = $i ]];then
        echo "    Skipping; file is in skip list"
        exit
    fi
done

# Copy the file to a temporary location
WORKFILE="$XMLFILE.transform"
cp $XMLFILE $WORKFILE

# Replace entities
printf "%-64s" "    Replacing XML entities..."
php $ENT_REPLACE_SCRIPT $WORKFILE
if [ "$?" -ne "0" ];then
    printf " %7s \n" "[FAIL]"
    echo "    FAILED: Replacing XML entities in $XMLFILE" >&2
    exit 1
fi
printf " %7s \n" "[DONE]"

printf "%-64s\n" "    Converting from DocBook 4 to 5..."
xsltproc $UPGRADE_XSL $WORKFILE > $WORKFILE.db5
if [ `stat --print="%s" $WORKFILE.db5` -lt 200 ];then
    printf " %7s \n" "[FAIL]"
    echo "    FAILED: Conversion of $XMLFILE" >&2
    exit 1
fi
printf " %7s \n" "[DONE]"

# Overwrite working file with transformed content
mv $WORKFILE.db5 $WORKFILE

# Restore entities
printf "%-64s" "    Restoring XML entities..."
php $ENT_RESTORE_SCRIPT $WORKFILE
if [ "$?" -ne "0" ];then
    printf " %7s\n" "[FAIL]"
    echo "    FAILED: Restoring XML entities in $XMLFILE" >&2
    exit 1
fi
printf " %7s\n" "[DONE]"

printf "%-64s" "    Stripping conversion comment..."
sed --regexp-extended --in-place 's///' $WORKFILE
if [ "$?" -ne "0" ];then
    printf " %7s\n" "[FAIL]"
    echo "    FAILED: Stripping DB4 conversion comments in $XMLFILE" >&2
    exit 1
fi
printf " %7s\n" "[DONE]"

printf "%-64s" "    Adding XML declaration..."
php $XML_INTRO_SCRIPT $WORKFILE
if [ "$?" -ne "0" ];then
    printf " %7s\n" "[FAIL]"
    echo "    FAILED: Adding XML declaration in $XMLFILE" >&2
    exit 1
fi
printf " %7s\n" "[DONE]"

printf "%-64s" "    Fixing programlisting blocks..."
php $XML_PL_SCRIPT $WORKFILE 1>&2
if [ "$?" -ne "0" ];then
    printf " %7s\n" "[FAIL]"
    echo "    FAILED: Fixing program listings in $XMLFILE" >&2
    exit 1
fi
printf " %7s\n" "[DONE]"

mv $WORKFILE $XMLFILE
exit 0
```

The above script satisfies the bullet points I laid out. More importantly,
between the two scripts, I can now process an entire tree of files. As an
example:

```bash
$ path/to/upgradeDocbookBulk 2>&1 | tee -a error.log
```

I can then grep the error log for "FAIL" to see what failures I might have had:

```bash
$ grep FAIL error.log
```

Because a file is not moved if a failure happens, I can then look at the
differences between the original and the converted version, and determine what
the issue may be. (Hint: in the entire ZF2 documentation tree, I only had a
handful of errors or less. In all cases, they were due to non-validating XML.)

Conclusions
-----------

Converting from DocBook4 to DocBook5 is a non-trivial task, but, fortunately,
one that can be fairly easily automated — assuming you know what the
`db4-upgrade.xsl` script does and does not do. Hopefully, this post sheds some
light on that, and helps describe a process you can use and/or modify to perform
a comprehensive migration.

I've put the scripts together in a repository on [GitHub](http://github.com):

- [https://github.com/weierophinney/docbook5-migration](https://github.com/weierophinney/docbook5-migration)

Feel free to clone, fork, etc.
