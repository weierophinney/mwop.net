<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('on-error-handling-and-closures');
$entry->setTitle('On Error Handling and Closures');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1324052778);
$entry->setUpdated(1324052778);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
  1 => 'oop',
));

$body =<<<'EOT'
<p>
    The error suppression operator in PHP ("@") is often seen as a necessary 
    evil. Many, many low-level function will return a value indicating an error,
    but also raise an <code>E_NOTICE</code> or <code>E_WARNING</code> -- things
    you might be able to recover from, or conditions where you may want to raise
    an exception.
</p>

<p>
    So, at times, you find yourself writing code like this:
</p>

<div class="example"><pre><code lang="php">
if (false === ($fh = @fopen($filename, 'r'))) {
    throw new RuntimeException(sprintf(
        'Could not open file "%s" to read', $filename
    ));
}
</code></pre></div>

<p>
    Seems straight-forward enough, right? But it's wrong on so many levels.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<ul>
    <li>
        The error doesn't magically go away. If you've got PHP's log setup, 
        you're going to be getting a log entry each time the suppressed 
        statement errors.
    </li>

    <li>
        Error suppression is expensive. Like, really, really expensive. A 
        special error handler is registered to prevent the error propagating
        to the display (if <code>display_errors</code> is enabled), but errors
        are still sent to the log (as noted above). When done, the original
        error handler has to be restored.
    </li>

    <li>
        If you use things like <code>error_get_last()</code>, you may find that
        if you have many error suppressions, it returns something unrelated to
        the error that just occurred. 
    </li>

    <li>
        PHPUnit, anyone? Error suppression and PHPUnit do not play well 
        together. And there's a reason for that: often suppressed errors are
        indicative of bigger issues.
    </li>
</ul>

<p>
    So, how do you address it?
</p>

<p>
    PHP has two functions to assist with this: <code>set_error_handler()</code>
    and <code>restore_error_handler()</code>. The first takes a callable 
    argument, and optionally a mask of error levels to which it will respond;
    the second is used to return error handling to the previously set handler.
</p>

<div class="example"><pre><code lang="php">
function handleError($errno, $errmsg = '', $errfile = '', $errline = 0)
{
    throw new RuntimeException(sprintf(
        'Error reading file (in %s@%d): %s',
        $errfile, $errline, $errmsg
    ), $errno);
}

set_error_handler('handleError', E_WARNING);
$fh = fopen($filename, 'r');
restore_error_handler();
</code></pre></div>

<p>
    Traditionally, these have been a pain to use, as you have to create 
    individual functions or methods for handlers, and methods must have public
    visibility, even if the functionality is internal to the class.
</p>

<p>
    With PHP 5.3, we get a new option, however: closures. 
</p>

<p>
    With closures, error handlers are still a pain to use, but you now get to 
    scope the handlers directly in the context of the application flow. Let's
    look at an example:
</p>

<div class="example"><pre><code lang="php">
set_error_handler(
    function($error, $message = '', $file = '', $line = 0) use ($filename) {
        throw new RuntimeException(sprintf(
            'Error reading file "%s" (in %s@%d): %s',
            $filename, $file, $line, $message
        ), $error);
    },
    E_WARNING
);
$fh = fopen($filename, 'r');
restore_error_handler();

</code></pre></div>

<p>
    If you just want to ignore the error, it's even simpler:
</p>

<div class="example"><pre><code lang="php">
set_error_handler(function() { return true; }, E_NOTICE);
$contents = file_get_contents($filename);
restore_error_handler();
</code></pre></div>

<p>
    The code isn't necessarily succinct, which is one reason many gravitate
    towards using error suppression instead. However, it has the benefit of
    being context-sensitive and robust, which is always a good goal.
</p>
EOT;
$entry->setExtended($extended);

return $entry;