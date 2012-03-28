<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('140-Globals,-continued');
$entry->setTitle('Globals, continued');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1179678230);
$entry->setUpdated(1179703781);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    <b>Update:</b> Sara has pointed out a flaw in my last case. The file 'loadFileWithGlobals.php' was incorrectly loading the wrong file -- it should be loading 'withGlobals2.php' (updated now). When it does, access to 'baz2' works as it should.
</p>
<p>
    As I note to in my comment, however, I stand by my original rant: relying on globals for your applications is a bad practice, as it makes them difficult to integrate with other applications later. Developers using your application should not need to hunt down exactly when a global is first declared and explicitly push it into the global scope in order to get that application to integrate with others. Use other means, such as singletons or registries, to persist configuration within your applications.
</p>
<hr />
<p>
    In <a href="/matthew/index.php?url=archives/139-PHP-globals-for-the-OOP-developer.html">my last entry</a>, 
    I evidently greatly simplified the issue to the point that my example
    actually didn't display the behaviour I had observed. I'm going to show a
    more detailed example that shows exactly the behaviour that was causing
    issues for me.
</p>

<p>
    First off, this has specifically to do with including files from within
    functions or class methods that then call on other files that define values
    in the global scope. In the original example, I show an action controller
    method that includes the serendipity bootstrap file, which in turn loads a
    configuration file that sets a multi-dimensional array variable in the
    global scope. Without first defining the variable in the global scope, this
    method of running serendipity fails.
</p>

<p>
    Now, for the examples.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    First, let's define six files. Four set variables, two by regular
    declaration, the other two by declaring using <kbd>$GLOBALS</kbd>. The other
    two files each load one of these and act on the variables set.
</p>

<div class="example"><pre><code lang="php">
&lt;?php
// File: withoutGlobals.php
$bar = 'baz';
?&gt;

&lt;?php
// File: withoutGlobals2.php
$bar2 = 'baz2';
?&gt;

&lt;?php
// File: withGlobals.php
$GLOBALS['baz'] = 'bat';
?&gt;

&lt;?php
// File: withGlobals2.php
$GLOBALS['baz2'] = 'bat2';
?&gt;

&lt;?php
// File: loadFileWithoutGlobals.php
include dirname(__FILE__) . '/withoutGlobals2.php';

echo 'Direct access to bar2: ', $bar2, \&quot;\n\&quot;;
echo 'GLOBALS access to bar2: ', $GLOBALS['bar2'], \&quot;\n\&quot;;
?&gt;

&lt;?php
// File: loadFileWithGlobals.php
include dirname(__FILE__) . '/withGlobals2.php';

echo 'Direct access to baz2: ', $baz2, \&quot;\n\&quot;;
echo '$GLOBALS access to baz2: ', $GLOBALS['baz2'], \&quot;\n\&quot;;
?&gt;
</code></pre></div>

<p>
    Now, I'll define a class, <kbd>MyFoo</kbd>, that tries in a variety of ways
    to set and access global values:
</p>

<div class="example"><pre><code lang="php">
&lt;?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);

class MyFoo
{
    public function setGlobal()
    {
        $GLOBALS['foo'] = 'bar';
    }

    public function loadFileWithoutGlobals()
    {
        include dirname(__FILE__) . '/withoutGlobals.php';
    }

    public function loadFileWithGlobals()
    {
        include dirname(__FILE__) . '/withGlobals.php';
    }

    public function loadScriptThatCallsFileWithoutGlobals()
    {
        include dirname(__FILE__) . '/loadFileWithoutGlobals.php';
    }

    public function loadScriptThatCallsFileWithGlobals()
    {
        include dirname(__FILE__) . '/loadFileWithGlobals.php';
    }
}
</code></pre></div>

<p>
    Finally, we actually try a few cases:
</p>

<div class="example"><pre><code lang="php">
&lt;?php
$o = new MyFoo();

// Case 1; expect 'Foo: bar'
$o-&gt;setGlobal();
if (isset($foo)) {
    echo 'Foo: ', $foo, \&quot;\n\&quot;;
} else {
    echo \&quot;Foo not set\n\&quot;;
}

// Case 2; expect 'Bar not set'
$o-&gt;loadFileWithoutGlobals();
if (isset($bar)) {
    echo 'Bar: ', $bar, \&quot;\n\&quot;;
} else {
    echo \&quot;Bar not set\n\&quot;;
}

// Case 3; expect 'Baz: bat'
$o-&gt;loadFileWithGlobals();
if (isset($baz)) {
    echo 'Baz: ', $baz, \&quot;\n\&quot;;
} else {
    echo \&quot;Baz not set\n\&quot;;
}

// Case 4; expect failure
$o-&gt;loadScriptThatCallsFileWithoutGlobals();

// Case 5; expect failure
$o-&gt;loadScriptThatCallsFileWithGlobals();
</code></pre></div>

<p>
    Now, I was wrong about being able to declare globals using
    <kbd>$GLOBALS</kbd>; the first case, where I set 'foo', works fine.  Case 2
    works as I expect, too; since the variable was technically defined in the
    same scope as the method, it's not global. The third case, which I initially
    said didn't work in my last post, works as well; $baz is set correctly in
    the global scope.
</p>

<p>
    Cases 4 and 5 are where things get interesting. In Case 4, direct access to
    <kbd>$bar2</kbd> works because it's technically in the same scope as where
    it's defined. However, access to it via <kbd>$GLOBALS</kbd> fails, as
    expected, because it was not defined in the global scope.
</p>

<p>
    <strike>In case 5, <em>neither</em> access works; direct access to <kbd>$baz2</kbd>
    does not work, nor does access via <kbd>$GLOBALS</kbd>; in both cases, I get
    a notice indicating that the index is undefined. This was the exact
    situation that was causing issues for me, and precisely the sort of
    inconsistency that makes working with globals so frustrating.</strike>
    In the updated code, Case 5 works exactly as it should; <kbd>$baz2</kbd> is
    in the global scope.
</p>
EOT;
$entry->setExtended($extended);

return $entry;