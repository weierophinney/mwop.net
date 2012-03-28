<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('261-Code-Generation-with-ZendCodeGenerator');
$entry->setTitle('Code Generation with Zend\CodeGenerator');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1301065689);
$entry->setUpdated(1301104954);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
));

$body =<<<'EOT'
<p>
Zend Framework has offerred a code generation component since version 1.8, when
we started shipping <code>Zend_Tool</code>. <code>Zend_CodeGenerator</code> largely mimics PHP's
<code>ReflectionAPI</code>, but does the opposite: it instead generates code.
</p>

<p>
Why might you want to generate code?
</p>

<ul>
<li>
You can use it as an assistive form of "copy and paste" for common tasks (as
   an example, it's used in <code>zf.sh</code> to generate controller classes and action
   methods).
</li>
<li>
You might want to generate code from configuration, to remove the "compile"
   phase of generating objects from configuration values. This is often done to
   improve performance in situations that rely heavily on configurable values.
</li>
</ul>

<p>
<code>Zend\CodeGenerator</code> in the ZF2 repository is largely ported from Zend Framework
1, but also includes some functionality surrounding namespace usage and imports.
I used it this week when working on some prototypes, and found it useful enough
that I want to share some of what I've learned.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h1 id="toc_2">Basics</h1>

<p>
In most cases, you'll need to look through the API methods to get an idea of
what you can create. The various classes are all in the <code>Zend\CodeGenerator\Php</code>
namespace (the subnamespace is so that we might include code generation for
formats and languages other than PHP at some future point), and they include:
</p>

<ul>
<li>
<code>Docblock\Tag\LicenseTag</code> (generate "license" annotations for docblocks)
</li>
<li>
<code>Docblock\Tag\ParamTag</code> (generate "param" annotations for docblocks)
</li>
<li>
<code>Docblock\Tag\ReturnTag</code> (generate "return" annotations for docblocks)
</li>
<li>
<code>PhpBody</code> (generate arbitrary PHP content; typically to fill files or method
   calls)
</li>
<li>
<code>PhpClass</code> (generate PHP classes)
</li>
<li>
<code>PhpDocblock</code> (generate PHP docblocks)
</li>
<li>
<code>PhpDocblockTag</code> (generate arbitrary dockblock annotations)
</li>
<li>
<code>PhpFile</code> (generate PHP files)
</li>
<li>
<code>PhpMethod</code> (generate PHP class methods)
</li>
<li>
<code>PhpParameterDefaultValue</code> (generate default parameter values for
   PHP method/function arguments)
</li>
<li>
<code>PhpParameter</code> (generate PHP method/function parameters)
</li>
<li>
<code>PhpProperty</code> (generate PHP class properties)
</li>
<li>
<code>PhpPropertyValue</code> (generate PHP property value arguments; i.e., the default
   property value on instantiation)
</li>
<li>
<code>PhpValue</code> (generate arbitrary PHP value assignment statements)
</li>
</ul>

<p>
In most cases, you can call the <code>setContent()</code> and/or <code>setName()</code> methods; other
methods will be available based on context. All classes also contain a
<code>generate()</code> method which will generate code based on the current state of the
object.
</p>

<p>
Most of these classes aren't of much use in isolation, but instead interact with
other objects in order to create the expected code.
</p>

<p>
As an example, the prototype I was building was generating a PHP class file.
The requirements included:
</p>

<ul>
<li>
Setting the namespace
</li>
<li>
Defining one or more class imports
</li>
<li>
Defining a class, which extended another class
</li>
<li>
Defining several methods for that class, with code; in at least one case, the
   method generated also expected arguments
</li>
</ul>

<p>
This was actually relatively easy; the hardest part was generating the actual
code body for the individual methods!
</p>

<p>
As an example, we'll generate a class skeleton now:
</p>

<div class="example"><pre><code lang="php">
use Zend\CodeGenerator\Php as CodeGen;
$file = new CodeGen\PhpFile();
$file-&gt;setNamespace('Application')
     -&gt;setUses('Zend\Di\DependencyInjectionContainer', 'DIC');
     
$class = new CodeGen\PhpClass();
$class-&gt;setName('Context')
      -&gt;setExtendedClass('DIC');

$get = new CodeGen\PhpMethod();
$get-&gt;setName('get')
    -&gt;setParameters(array(
        new CodeGen\PhpParameter(array('name' =&gt; 'name')),
        new CodeGen\PhpParameter(array(
            'name' =&gt; 'params',
            'defaultValue' =&gt; new CodeGen\PhpParameterDefaultValue(array(
                'value' =&gt; array(),
            )),
        )),
    ));

$class-&gt;setMethod($get);

$file-&gt;setClass($class);

echo $file-&gt;generate();
</code></pre></div>

<p>
The above will generate the following:
</p>

<div class="example"><pre><code lang="php">
&lt;?php

namespace Application;

use Zend\Di\DependencyInjectionContainer as DIC;

class Context extends DIC
{

    public function get($name, $params = array())
    {
    }


}
</code></pre></div>

<p>
Some tips and gotchas:
</p>

<ul>
<li>
As in most of ZF, any setter method can be configured. Key names correspond
   to the setter method, minus "set", and with the first letter lowercased --
   so, <code>setName()</code> can be triggered by passing a configuration key of "name";
   <code>setDefaultValue()</code> with "defaultValue".
</li>
   
<li>
You don't <em>need</em> to provide objects in most cases; you can pass arrays
   representing the configuration values for the object type expected. As an
   example, passing an array of values as an item to <code>setParameter()</code> will pass
   the configuration to the constructor of <code>PhpParameter</code>. That said, I found it
   was more predictable and easier to read to do the explicit object
   declarations.
</li>
   
<li>
If your default parameter value is an array, you have to jump through some
   hoops. Normally, you could simply specify the value you want to use to the
   <code>setDefaultValue()</code> method (or "defaultValue" key), but arrays are treated as
   configuration. As such, you will need to create a <code>PhpParameterDefaultValue</code>
   explicitly in these cases (as I did in the above example).
</li>

<li>
In the above, I didn't generate anything more than a skeleton. However, in my
   actual prototype, I was generating code for the body content of methods. I
   found that <code>sprintf</code> was my friend here, as was a variable or constant
   representing the amount of indentation. As an example:

<div class="example"><pre><code lang="php">
$caseStatements = array();
foreach ($definitions as $definition) {
    // ...
    
    $caseStatement  = '';
    foreach ($cases as $case) {
        $caseStatement .= sprintf(\&quot;%scase '%s':\n\&quot;, $indent, $case);
    }
    $caseStatement .= sprintf(\&quot;%sreturn \$this-&gt;%s();\n\&quot;, str_repeat($indent, 2), $getter);
    $caseStatements[] = $caseStatement;
}

$switch = sprintf(\&quot;switch (\$name) {\n%s}\&quot;, implode($caseStatements, \&quot;\n\&quot;));

$method-&gt;setBody($switch); // PhpMethod object
</code></pre></div>

   Which in turn generated the following:

<div class="example"><pre><code lang="php">
switch ($name) {
    case 'foo':
    case 'My\Component\Foo':
        $this-&gt;getMyComponentFoo();

}
</code></pre></div>
    </li>
</ul>


<h2 id="toc_2.1">Why?</h2>

<p>
It may look like a lot of code, and you may be wondering, "why bother?" The
point, though, is that it's predictable and testable -- which gives it a nudge
over a templated solution. I can basically ensure the structure I want similar
to constructing XML using <code>DOM</code> -- and alter it later if I want to.
</p>

<p>
Additionally, in my particular use case -- and, really, it's a common use 
case -- I'm using a predictable configuration structure, and want to generate
something over and over again. As my configuration changes, I want to be able
to update the code, without needing to worry if I forgot something or
introduced a new typo (other than those I created in my configuration file). The
point is really that this is code I'll be writing again and again, so having a
tool to generate it will save me time.
</p>

<p>
In addition, in this particular use case, the generated code is faster than
running the code that generates it, as it prevents a "configuration" step in the
final production phase. By generating code, I can circumvent things such as
Reflection, use more efficient practices (e.g., usage of <code>call_user_func()</code> or
direct method calls instead of <code>call_user_func_array()</code>), and introduce type
hinting in an area that relied on strings previously.
</p>

<h2 id="toc_2.2">Fini</h2>

<p>
There's a ton of functionality available in <code>Zend\CodeGenerator</code>, and I only
scratched the tip of the iceberg in this post. What use cases do <em>you</em> have for
code generation? what tips to <em>you</em> have to share?
</p>
EOT;
$entry->setExtended($extended);

return $entry;