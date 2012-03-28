<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('117-Cgiapp2-Tutorial-1-Switch-Template-Plugins-at-Will');
$entry->setTitle('Cgiapp2 Tutorial 1: Switch Template Plugins at Will');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1149561123);
$entry->setUpdated(1149562908);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    This is the first in a short series of tutorials showcasing some of the new
    features of <a href="/phly/index.php?package=Cgiapp2">Cgiapp2</a>. In this
    tutorial, you will see how easy it is to switch template engines in
    Cgiapp2-based applications.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    Cgiapp2 implements a new callback hook system, which is basically an
    <a href="http://en.wikipedia.org/wiki/Observer_pattern">Observer pattern</a>. 
    Cgiapp2 has a number of registered hooks to which observers can attach; when
    a hook is triggered, each observer attached to it is notified and executed.
    Additionally, Cgiapp2 provides a means to create new hooks in your
    applications that others may observer; that's a subject for another post.
</p>
<p>
    Why all this talk about hooks? Because in Cgiapp2, the various template
    actions -- initialization, variable assignment, and rendering -- are
    relegated to hooks. For simplicity's sake, and for backward compatibility,
    you can use the functions tmpl_path(), tmpl_assign(), and load_tmpl() to
    invoke them; you could also use the generic call_hook() method to do so,
    passing the hook name as the first argument.
</p>
<p>
    To standardize template actions, I developed <a href="/phly/darcs/annotate/cgiapp/Cgiapp2/Plugin/Template/Interface.class.php">Cgiapp2_Plugin_Template_Interface</a>,
    a standard interface for template plugins. Any template plugin that
    implements this interface can be called with the standard tmpl_* methods --
    which means that developers can mix-and-match template engines at will!
</p>
<p>
    Since Cgiapp2 and its subclasses no longer need to be aware of the rendering
    engine, developers that are instantiating Cgiapp2-based applications can
    choose their own rendering engine at the time of instantiation:
</p>
<div class="example"><pre><code lang="php">
&lt;?php
require_once 'Some/Cgiapp2/Application.php';
require_once 'Cgiapp2/Plugin/Savant3.php';
$app = new Some_Cgiapp2_Application($options);
$app-&gt;run();
</code></pre></div>
<p>
    In the example above, developer X uses Savant3  as the template engine. Now,
    say you're developer Y, and have an affinity for Smarty, and want to use
    that engine for the application. No problem:
</p>
<div class="example"><pre><code lang="php">
&lt;?php
require_once 'Some/Cgiapp2/Application.php';
require_once 'Cgiapp2/Plugin/Smarty.php';
$app = new Some_Cgiapp2_Application($options);
$app-&gt;run();
</code></pre></div>
<p>
    Now all you have to do is create Smarty versions of the templates. Cgiapp2
    doesn't care to which engine it's rendering; it simply notifies the last
    registered template plugin.
</p>
<p>
    Stay tuned for more tutorials in the coming days!
</p>
EOT;
$entry->setExtended($extended);

return $entry;