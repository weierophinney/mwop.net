<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('265-Using-DocBlox');
$entry->setTitle('Using DocBlox');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1312394400);
$entry->setUpdated(1312526398);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
Until a few years ago, there were basically two tools you could use to generate
API documentation in PHP: <a href="http://phpdocumentor.org/">phpDocumentor</a> and
<a href="http://www.stack.nl/~dimitri/doxygen/">Doxygen</a>. phpDocumentor was long
considered the standard, with Doxygen getting notice when more advanced features
such as inheritance diagrams are required. However, phpDocumentor is practically
unsupported at this time (though a small group of developers is working on a new
version), and Doxygen has never had PHP as its primary concern.  As such, a
number of new projects are starting to emerge as replacements.
</p>

<p>
One of these is <a href="http://docblox-project.org">DocBlox</a>. I am well aware there are
several others -- and indeed, I've tried several of them. This post is not here
to debate the merits or demerits of this or other solutions; the intention is to
introduce you to DocBlox so that you can evaluate it yourself.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2 id="toc_1.1">Getting DocBlox</h2>

<p>
DocBlox can be installed in a variety of ways:
</p>

<ul>
<li>
You can checkout the project <a href="http://github.com/mvriel/docblox">via GitHub</a>.
</li>
<li>
You can <a href="https://github.com/mvriel/Docblox/zipball/master">download a snapshot</a>.
</li>
<li>
You can <a href="https://github.com/mvriel/Docblox/zipball/v0.12.2">download a release</a>.
</li>
<li>
You can use <a href="http://pear.docblox-project.org/">use the PEAR installer</a>.
</li>
</ul>

<p>
I personally prefer using the PEAR installer, as it's as simple as this:
</p>

<div class="example"><pre><code lang="bash">
prompt&gt; pear channel-discover pear.michelf.com
prompt&gt; pear channel-discover pear.docblox-project.org
prompt&gt; pear install -a docblox/DocBlox-beta
</code></pre></div>

<p>
The first <code>channel-discover</code> is to grab a third-party package optionally used in
the rendering process to convert Markdown in the descriptions to HTML. And don't
let the "beta" status fool you -- this project is quite stable at this point;
the author, <a href="http://blog.naenius.com">Mike van Riel</a>, is simply being
conservative as he rounds out features.
</p>

<p>
If you are checking out the project via Git or a snapshot, you simply need to
expand the archive and make a note of its location -- when I've used this method
in the past, I usually create a symlink to the <code>bin/docblox.php</code> script in my
path:
</p>

<div class="example"><pre><code lang="bash">
prompt&gt; ln -s path/to/docblox/bin/docblox.php ~/bin/docblox
</code></pre></div>

<h2 id="toc_1.2">Using DocBlox</h2>

<p>
Once you have installed DocBlox, how do you use it? It's really quite easy:
</p>

<div class="example"><pre><code lang="bash">
prompt&gt; cd some/project/of/yours/
prompt&gt; mkdir -p documentation/api/
prompt&gt; docblox run -d path/to/source/ -t documentation/api/
</code></pre></div>

<p>
At this point, DocBlox will merrily scan your source located in
<code>path/to/source</code>, and build API
documentation using its default HTML templates for you in <code>documentation/api</code>.
Once complete, you can point your browser at <code>documentation/api/index.html</code> and
start browsing your API documentation.
</p>

<h2 id="toc_1.3">Using DocBlox to identify missing docblocks</h2>

<p>
While running, you may see some notices in your output stream, like the
following:
</p>

<pre>
2011-08-02T16:08:34-05:00 ERR (3): No DocBlock was found for Property $request in file Mvc/Route/RegexRoute.php on line 16
</pre>

<p>
This output is invaluable for identifying places you've omitted docblocks in
your code. You can capture this information pretty easily using <code>tee</code>:
</p>

<div class="example"><pre><code lang="bash">
prompt&gt; docblox run -d path/to/source/ -t documentation/api/ 2&gt;&amp;1 | tee -a docblox.log
</code></pre></div>

<p>
I recommend doing this whenever running DocBlox, going through the output, and
adding docblocks wherever you encounter these errors.
</p>

<p>
(You can do similarly using tools such as <a href="http://pear.php.net/PHP_CodeSniffer">PHP_CodeSniffer</a>. 
More tools is never a bad thing, though.)
</p>

<p>
If you want to disable the verbosity, however, you can, by passing either the
<code>-q</code> or <code>--quiet</code> options.
</p>

<h2 id="toc_1.4">Class Diagrams</h2>

<p>
DocBlox will try and generate class diagrams by default. In order to do this,
you need to have <a href="http://www.graphviz.org/">GraphViz</a> installed somewhere on your
path. The results are pretty cool, however -- you can zoom in and out of the
diagram, and click on classes to get to the related API documentation.
</p>

<p>
(The class diagram is typically linked from the top of each page.)
</p>

<h2 id="toc_1.5">Specifying an alternate title</h2>

<p>
By default, DocBlox uses its own logo and name as the title of the
documentation and in the "header" line of the output. You can change this using
the <code>--title</code> switch:
</p>

<div class="example"><pre><code lang="bash">
prompt&gt; docblox run -d path/to/source/ -t documentation/api/ --title \&quot;My Awesome API Docs\&quot;
</code></pre></div>


<h2 id="toc_1.6">Using alternate templates</h2>

<p>
While the default template of DocBlox is reasonable, one of its initial selling
points to me was the fact that you could conceivably create new templates.
In order to test this out, and also iron out some of the kinks, Mike wrote
templates for a few PHP OSS projects, including Zend Framework and Agavi.
Templates need to be in a location DocBlox can find them -- in
<code>DocBlox/data/themes</code> under your PEAR install, or simply <code>data/themes</code> if you
installed a release tarball. Invoking a theme is as easy as using the
<code>--template</code> argument:
</p>

<div class="example"><pre><code lang="bash">
prompt&gt; docblox run -d path/to/source/ -t documentation/api/ --title \&quot;My Awesome API Docs\&quot; --template zend
</code></pre></div>

<p>
Try out each of the provided themes to see which you might like best -- and
perhaps try your hand at writing a theme. Each given theme is simply an XML file
and a small set of XSL stylesheets, and optionally CSS and images to use with
the generated markup.
</p>

<h2 id="toc_1.7">Iterative documentation</h2>

<p>
When you generate documentation, DocBlox actually creates a SQLite database in
which to store the information it learns while parsing your code base. This
allows it to be very, very fast both when parsing (it can free information from
memory once it's done analyzing a class or file) as well as when transforming
into output (as it can iteratively query the database for structures).
</p>

<p>
What does this mean for you?
</p>

<p>
Well, first, if you want to try out new templates, it won't need to re-parse
your source code -- it simply generates the new output from the already parsed
definitions. This can be very useful particularly when creating new templates.
Generation is oftentimes instantaneous for small projects.
</p>

<p>
Second, it means that you can build the full documentation once, and only
periodically update it (which you can do using the <code>--force</code> option). This is
particularly useful for build processes.
</p>

<h2 id="toc_1.8">Configuration</h2>

<p>
One problem with any rich CLI tool is that you often get a proliferation of
options, and remembering them between invocations can be hard (particularly if
you only run the tool during releases). DocBlox allows you to create a
configuration file, <code>docblox.xml</code>, in your project. The format is relatively
simple; the (mostly) equivalent to the above options I've used is as below:
</p>

<div class="example"><pre><code lang="xml">
&lt;?xml version=\&quot;1.0\&quot; encoding=\&quot;UTF-8\&quot; ?&gt;                                     
&lt;docblox&gt;
    &lt;parser&gt;
        &lt;target&gt;documentation/api&lt;/target&gt;
    &lt;/parser&gt;
    &lt;transformer&gt;
        &lt;target&gt;documentation/api&lt;/target&gt;
    &lt;/transformer&gt;
    &lt;files&gt;
        &lt;directory&gt;path/to/source&lt;/directory&gt;
    &lt;/files&gt;
    &lt;transformations&gt;
        &lt;template&gt;
            &lt;name&gt;zend&lt;/name&gt;
        &lt;/template&gt;
    &lt;/transformations&gt;
&lt;/docblox&gt;
</code></pre></div>

<p>
You can't specify the title in the configuration, but often that will be
template-driven, anyways.
</p>

<p>
DocBlox will then look for this file in the current directory and simply use
it, allowing you to invoke it as follows:
</p>

<div class="example"><pre><code lang="bash">
prompt&gt; docblox run
</code></pre></div>

<p>
Or you can specify the configuration file yourself:
</p>

<div class="example"><pre><code lang="bash">
prompt&gt; docblos run -c config.xml
</code></pre></div>

<p>
(Side note: on the release current as of when I write, 0.12.2, I have not
successfully been able to specify the template name.)
</p>

<h2 id="toc_1.9">Search</h2>

<p>
If you look carefully at the generated output, you'll notice a search box. By
default, this doesn't work... because it points to a PHP script! When installed
on a server capable of serving PHP, however, it can be used to help find
classes, methods, and more. As an example, you can
<a href="http://framework.zend.com/apidoc/1.11/">search the Zend Framework 1.11 API documentation</a>.
</p>

<h2 id="toc_1.10">Conclusions</h2>

<p>
Hopefully this tutorial will get you started investigating DocBlox. I've been
quite happy with what I've seen so far of the project, and gladly recommend it.
There are other alternatives, however, and I also suggest you try those out;
<a href="http://blog.liip.ch/archive/2011/07/26/phpdoc-compilers-and-inheritdoc.html">Liip recently published a comparison of features</a>, and that article can be used
as a starting point for your own investigations.
</p>

<p>
<em>(Disclosure I've contributed a few patches and some advice to Mike van Riel as he's developed DocBlox).</em> 
</p>
EOT;
$entry->setExtended($extended);

return $entry;