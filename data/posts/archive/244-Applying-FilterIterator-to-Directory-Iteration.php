<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('244-Applying-FilterIterator-to-Directory-Iteration');
$entry->setTitle('Applying FilterIterator to Directory Iteration');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1281969000);
$entry->setUpdated(1282333521);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'spl',
));

$body =<<<'EOT'
<p>
    I'm currently doing research and prototyping for autoloading alternatives in
    <a href="http://framework.zend.com/">Zend Framework</a> 2.0. One approach
    I'm looking at involves creating explicit class/file maps; these tend to be
    much faster than using the <code>include_path</code>, but do require some
    additional setup.
</p>

<p>
    My algorithm for generating the maps was absurdly simple:
</p>

<ul>
    <li>Scan the filesystem for PHP files</li>
    <li>If the file does not contain an interface, class, or abstract class,
    skip it.</li>
    <li>If it does, get its declared namespace and classname</li>
</ul>

<p>
    The question was what implementation approach to use.
</p>

<p>
    I'm well aware of <code>RecursiveDirectoryIterator</code>, and planned to
    use that. However, I also had heard of <code>FilterIterator</code>, and
    wondered if I could tie that in somehow. In the end, I could, but the
    solution was non-obvious.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>What I Thought I'd Be Able To Do</h2>

<p>
    <code>FilterIterator</code> is an abstract class. When extending it, you must
    define an <code>accept()</code> method. 
</p>

<div class="example"><pre><code lang="php">
class FooFilter extends FilterIterator
{
    public function accept()
    {
    }
}
</code></pre></div>
    
<p>
    In that method, you typically will inspect whatever is returned by
    <code>$this->current()</code>, and then return a boolean <code>true</code>
    or <code>false</code>, depending on whether you want to keep it or not. 
</p>

<div class="example"><pre><code lang="php">
class FooFilter extends FilterIterator
{
    public function accept()
    {
        $item = $this-&gt;current();

        if ($someCriteriaIsMet) {
            return true;
        }

        return false;
    }
}
</code></pre></div>
    
<p>
    I'll go into the mechanics of my criteria later; what's important now is
    knowing that a <code>FilterIterator</code> allows you to limit the results
    returned by your iterator.
</p>

<p>
    I originally thought I'd be able to simply pass a
    <code>DirectoryIterator</code> or
    <code>RecursiveDirectoryIterator</code> to my filtering instance. This
    worked in the former case, as it's only one level deep. However, for the
    latter, it would only return the first directory level for all classes that
    matched -- i.e., if I ran it over "Zend/Controller", I'd get a match for
    each class under "Zend/Controller/Action/Helper/", but it would return
    simply "Zend/Controller/Action" as the match. This certainly wasn't useful.
</p>

<p>
    I then discovered <code>RecursiveFilterIterator</code>, which looked like it
    would solve the recursion problem. However, I found one of two results
    occurred: either I'd receive an entire subtree if at least one item matched,
    or it would skip an entire subtree if the first item found failed the
    criteria. There was no middle ground.
</p>

<h2>The Solution</h2>

<p>
    The solution was incredibly simple and elegant, once I stumbled upon it:
    pass my <code>RecursiveIteratorIterator</code> instance to the
    <code>FilterIterator</code>. 
</p>

<div class="example"><pre><code lang="php">
$rdi      = new RecursiveDirectoryIterator($somePath);
$rii      = new RecursiveIteratorIterator($rdi);
$filtered = new FooFilter($rii);
</code></pre></div>
    
<p>
    Really. It was that simple -- but, as noted, non-obvious. It also required a
    slight change within my filter -- instead of using <code>current()</code>,
    I'd need to first pull the "inner" iterator instance:
    <code>$this->getInnerIterator()->current()</code>. I show an example of that
    below when I go over the filter implementation.
</p>

<p>
    As for my criteria, I had several options. I could <code>require_once</code>
    the file, and use the Reflection API to inspect the class to determine if it
    was an interface, abstract class, or class, as well as to determine the
    namespace. However, I couldn't be 100% sure the file would contain a class,
    so this seemed like overkill. That, and horribly non-performant, due to
    using reflection.
</p>

<p>
    The next option was to simply slurp in the file contents into a variable,
    and use regular expressions. I love regular expressions, but in this case,
    it felt like I could possibly end up with some false positives. Also, since
    some of these files could be quite large, I was worried again about
    performance implications -- I don't want to have to wait forever to generate
    these maps.
</p>

<p>
    The solution I went with was to use the <a
        href="http://php.net/tokenizer">tokenizer</a> to inspect the file.
    Tokenizing is incredibly fast, and it's also incredibly simple to analyze
    the tokens.
</p>

<p>
    I decided to store the detected namespace and classnames as public
    properties of the <code>SplFileInfo</code> objects returned; this makes it
    simple to iterate over the entire collection and utilize that
    information. Also, because I have <code>SplFileInfo</code> objects, I
    already have the paths I need.
</p>

<p>
    My implementation looks like this:
</p>

<div class="example"><pre><code lang="php">
/** @namespace */
namespace Zend\File;

// import SPL classes/interfaces into local scope
use DirectoryIterator,
    FilterIterator,
    RecursiveIterator,
    RecursiveDirectoryIterator,
    RecursiveIteratorIterator;

/**
 * Locate files containing PHP classes, interfaces, or abstracts
 * 
 * @package    Zend_File
 * @license    New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class ClassFileLocater extends FilterIterator
{
    /**
     * Create an instance of the locater iterator
     * 
     * Expects either a directory, or a DirectoryIterator (or its recursive variant) 
     * instance.
     * 
     * @param  string|DirectoryIterator $dirOrIterator 
     * @return void
     */
    public function __construct($dirOrIterator = '.')
    {
        if (is_string($dirOrIterator)) {
            if (!is_dir($dirOrIterator)) {
                throw new InvalidArgumentException('Expected a valid directory name');
            }

            $dirOrIterator = new RecursiveDirectoryIterator($dirOrIterator);
        }
        if (!$dirOrIterator instanceof DirectoryIterator) {
            throw new InvalidArgumentException('Expected a DirectoryIterator');
        }

        if ($dirOrIterator instanceof RecursiveIterator) {
            $iterator = new RecursiveIteratorIterator($dirOrIterator);
        } else {
            $iterator = $dirOrIterator;
        }

        parent::__construct($iterator);
        $this-&gt;rewind();
    }

    /**
     * Filter for files containing PHP classes, interfaces, or abstracts
     * 
     * @return bool
     */
    public function accept()
    {
        $file = $this-&gt;getInnerIterator()-&gt;current();

        // If we somehow have something other than an SplFileInfo object, just 
        // return false
        if (!$file instanceof \SplFileInfo) {
            return false;
        }

        // If we have a directory, it's not a file, so return false
        if (!$file-&gt;isFile()) {
            return false;
        }

        // If not a PHP file, skip
        if ($file-&gt;getBasename('.php') == $file-&gt;getBasename()) {
            return false;
        }

        $contents = file_get_contents($file-&gt;getRealPath());
        $tokens   = token_get_all($contents);
        $count    = count($tokens);
        $i        = 0;
        while ($i &lt; $count) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                // single character token found; skip
                $i++;
                continue;
            }

            list($id, $content, $line) = $token;

            switch ($id) {
                case T_NAMESPACE:
                    // Namespace found; grab it for later
                    $namespace = '';
                    $done      = false;
                    do {
                        ++$i;
                        $token = $tokens[$i];
                        if (is_string($token)) {
                            if (';' === $token) {
                                $done = true;
                            }
                            continue;
                        }
                        list($type, $content, $line) = $token;
                        switch ($type) {
                            case T_STRING:
                            case T_NS_SEPARATOR:
                                $namespace .= $content;
                                break;
                        }
                    } while (!$done &amp;&amp; $i &lt; $count);

                    // Set the namespace of this file in the object
                    $file-&gt;namespace = $namespace;
                    break;
                case T_ABSTRACT:
                case T_CLASS:
                case T_INTERFACE:
                    // Abstract class, class, or interface found

                    // Get the classname
                    $class = '';
                    do {
                        ++$i;
                        $token = $tokens[$i];
                        if (is_string($token)) {
                            continue;
                        }
                        list($type, $content, $line) = $token;
                        switch ($type) {
                            case T_STRING:
                                $class = $content;
                                break;
                        }
                    } while (empty($class) &amp;&amp; $i &lt; $count);

                    // If a classname was found, set it in the object, and 
                    // return boolean true (found)
                    if (!empty($class)) {
                        $file-&gt;classname = $class;
                        return true;
                    }
                    break;
                default:
                    break;
            }
            ++$i;
        }

        // No class-type tokens found; return false
        return false;
    }
}
</code></pre></div>

<p>
    <i>Note: the Exceptions thrown in this class are defined in the same
    namespace; I'll leave how they're implemented to your imagination.</i>
</p>

<h2>Iterating Faster</h2>

<p>
    The next trick I discovered was in the form of
    <code>iterator_apply()</code>. Normally when I use iterators, I use
    <code>foreach</code>, because, well, that's what you do. But in looking
    through the various iterators for this exercise, I stumbled across this gem.
</p>

<p>
    Basically, you pass the iterator, a callback, and argument(s) you want
    passed to the callback. Like <code>FilterIterator</code>, you don't get the
    actual item returned by the iterator, so in most use cases, you pass the
    iterator itself:
</p>

<div class="example"><pre><code lang="php">
iterator_apply($it, $callback, array($it));
</code></pre></div>

<p>
    You can then grab the current value and/or key from the iterator itself:
</p>

<div class="example"><pre><code lang="php">
public function process(Iterator $it)
{
    $value = $it-&gt;current();
    $key   = $it-&gt;key();
    // ...
}
</code></pre></div>

<p>
    While you can use any valid PHP callback, I found the most interesting
    solution was to use a closure, as it allows you to define everything up
    front:
</p>

<div class="example"><pre><code lang="php">
iterator_apply($it, function() use ($it) {
    $value = $it-&gt;current();
    $key   = $it-&gt;key();
    // ...
});
</code></pre></div>

<p>
    If you pass in a local value via a <code>use</code> statement, you can do
    some aggregation:
</p>

<div class="example"><pre><code lang="php">
$map = new \stdClass;
iterator_apply($it, function() use ($it, $map) {
    $file = $it-&gt;current();
    $namespace = !empty($file-&gt;namespace) ? $file-&gt;namespace . '\\' : '';
    $classname = $namespace . $file-&gt;classname;
    $map-&gt;{$classname} = $file-&gt;getPathname();
});
</code></pre></div>

<p>
    Not only is this a nice, concise technique, it's also tremendously fast -- I
    was finding it was 200% - 300% faster than using a traditional
    <code>foreach</code> loop. Clearly it cannot be used in all situations, but
    if you <em>can</em> use it, you probably should.
</p>

<p>
    So, start playing with <code>FilterIterator</code> and
    <code>iterator_apply()</code> if you haven't already -- the two offer
    tremendous possibilities and capabilities for your applications.
</p>
EOT;
$entry->setExtended($extended);

return $entry;