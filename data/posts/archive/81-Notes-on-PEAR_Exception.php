<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('81-Notes-on-PEAR_Exception');
$entry->setTitle('Notes on PEAR_Exception');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1120703179);
$entry->setUpdated(1121009794);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I've been doing some thinking on exceptions, and <a
        href="http://pear.php.net/package/PEAR/docs/1.3.3.1/PEAR/PEAR_Exception.html">PEAR_Exception</a> in
    particular. You may want to skip ahead to read about how to use
    PEAR_Exception, as well as some of my thoughts on the class on first use. If
    you want the background, read on.
</p>
<p>
    I've created a package proposal on PEAR for a package called <a
        href="http://pear.php.net/pepr/pepr-proposal-show.php?id=263">File_Fortune</a>,
    an OOP interface to reading and writing fortune files. I've been using a
    perl module for this on the family website for years, and now that I'm
    starting work on the PHP conversion, I thought I'd start with the building
    blocks.
</p>
<p>
    In creating the proposal, I started with a PHP5-only version, though I found
    that I wasn't using much in PHP5 beyond the public/private/protected/static
    keywords. For error handling, I decided to try out <a
        href="http://pear.php.net/packages/PEAR_ErrorStack">PEAR_ErrorStack</a>,
    as I'd been hearing buzz about it being the new "preferred" method for error
    handling in PEAR. (Honestly, after using it, I'm not too happy with it;
    throwing PEAR_Errors was much easier, and easier to manipulate as well --
    but that's a subject for another post -- and exceptions were easier still,
    though more typing.)
</p>
<p>
    The first comment I got on the proposal was <em>the</em> question: "Why
    PHP5?" (<a href="http://paul-m-jones.com/blog/">Paul</a> wasn't too
    surprised by that reaction.) I thought about it, and decided it wasn't
    really all that necessary, beyond the fact that I'd need to take some extra
    steps to be able to actually test a PHP4 version. So, I did a PHP4 version.
</p>
<p>
    Well, then some chatter happened, and a number of developers said, "Why
    <em>not</em> PHP5?" So, I went back to PHP5. And then somebody else said,
    "Use PEAR_Exception." So, I started playing with that, and we finally get to
    the subject of this post.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    Exception handling is one of the advances PHP5 brought to PHP. I was very
    excited to have it available, as I'm accustomed to exception handling in
    perl (which is actually quite different than PHP's model, but the basics are
    the same). When I saw the suggestion to use it, I realized that exception
    handling would make the package solidly a PHP5 package. Simultaneously, I
    wondered why it hadn't occurred to me. Guess I've been coding more PHP than
    perl for a while now...
</p>
<p>
    The problem is that there's very little documentation on PEAR_Exception, and
    the tips I got on list, while helpful in getting my proposal out the door,
    left me with a lot of questions.
</p>
<p>
    For those who haven't used PEAR_Exception, here's the basics:
</p>
<ul>
    <li>Create a file in which to hold your exceptions classes. (Yes, plural;
    I'll get to that). If you're developing a PEAR-style package, you want to
    put it in the directory pertaining to your package name. So, since I was
    developing File_Fortune, my exception class became
    File/Fortune/Exception.php.</li>
    <li>Create a base exception class for your class that extends
    PEAR_Exception:
    <pre>
        class File_Fortune_Exception extends PEAR_Exception
        {
        }
    </pre>
    Note: it doesn't override anything. It just creates a pseudo-namespace.
    </li>
    <li>For each unique exception type, extend your base class:
    <pre>
        class File_Fortune_FileException extends File_Fortune_Exception
        {
        }

        class File_Fortune_HeaderException extends File_Fortune_Exception
        {
        }
    </pre>
    (Yes, you should create docblocks for each, describing their purpose.)
    </li>
    <li>In your code, throw exceptions instead of raising errors:
    <pre>
        if (false === ($fh = fopen($filename))) {
            throw new File_Fortune_FileException('Unable to open file');
        }
    </pre>
    </li>
    <li>In your phpDoc blocks, use '@throws Exception_Class_Name' with some
    descriptive text</li>
</ul>
<p>
    And that's it in a nutshell.
</p>
<p>
    The beauty of it is that you can really separate errors from return values
    -- errors are no longer a possible return value:
</p>
<pre>
    try {
        $fortune = $ff->getRandom();
    } catch (File_Fortune_Exception $e) {
        echo "Couldn't get fortune: " . $e->getMessage();
    }
</pre>
<p>
    The problem I saw with the system is that you end up with a bunch of
    exception classes, each of which has a veeeerrryyy looooonnnnngggg name,
    which leads to lots of typing, the possibility for typos (I had one in the
    version I used for the call to votes), and the possibility for more error
    handling than code, depending on the number of possible exceptions and how
    carefully you want to check for them:
</p>
<pre>
    try {
        $fortune = $ff->getRandom();
    } catch (File_Fortune_BadHeaderFileException $e) {
        echo "Could not parse header file";
    } catch (File_Fortune_HeaderFileException $e) {
        echo "Could not open header file";
    } catch (File_Fortune_BadFileException $e) {
        echo "Badly formed fortune file";
    } catch (File_Fortune_Exception $e) {
        // Catch-all for File_Fortune errors...
        echo "Couldn't get fortune: " . $e->getMessage();
    }
</pre>
<p>
    I got to thinking that there must be a better way. I haven't actually come
    up with one yet, though. My idea so far, however, is to have a single
    exception class, and in it define a number of class constants or statics --
    much like PEAR_Error/PEAR_ErrorStack, where they map to integer values --
    and to have these values map to actual error messages (which could possibly
    be localized within the class as well). Then, when throwing an error, it
    might be something like:
</p>
<pre>
    if (false === ($fh = fopen($filename))) {
        throw new File_Fortune_Exception(1);
    } 
    // or
    if (false === ($fh = fopen($filename))) {
        throw new File_Fortune_Exception(File_Fortune_Exception::FILE);
    } 
</pre>
<p>
    The constructor would be overridden to set the code and message based on the
    code passed (if a string was passed, that would be the message). Then you
    could test for a single exception class, and use $e-&gt;getCode() to check
    for the type if you need more fine-grained control.
</p>
<p>
    I'd be more than happy to discuss possibilities. Exceptions are a fantastic
    way to check for truly exceptional behaviour in code; in PHP5, they also
    seem to be incredibly fast and lightweight (though I have no substantive
    data to back that statement, other than API responsiveness). I'd like to see
    more people developing with them.
</p>
<p>
    On that note, what do other PHP develpers think of exception handling? I've
    heard some say it's too 'goto-ish' (I'm not sure I follow that train of
    thought), others prefer the simplicity of PEAR_Error. Leave a comment!
</p>
EOT;
$entry->setExtended($extended);

return $entry;