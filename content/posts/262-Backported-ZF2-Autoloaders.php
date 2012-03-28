<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('262-Backported-ZF2-Autoloaders');
$entry->setTitle('Backported ZF2 Autoloaders');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1305035460);
$entry->setUpdated(1306164515);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
  2 => 'zf2',
));

$body =<<<'EOT'
<p>
In the past six weeks, I've delivered both a webinar and a tutorial on Zend
Framework 2 development patterns. The first pattern I've explored is our new
suite of autoloaders, which are aimed at both performance and rapid application
development -- the latter has always been true, as we've followed PEAR
standards, but the former has been elusive within the 1.X series.
</p>

<p>
Interestingly, I've had quite some number of folks ask if they can use the new
autoloaders in their Zend Framework 1 development. The short answer is "yes,"
assuming you're running PHP 5.3 already. If not, however, until today, the
answer has been "no."
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
I've recently backported the ZF2 autoloaders to PHP 5.2, and posted them on GitHub:
</p>

<dl>
<dd>
<a href="https://github.com/weierophinney/zf-examples/tree/feature%2Fzf1-classmap/zf1-classmap">https://github.com/weierophinney/zf-examples/tree/feature%2Fzf1-classmap/zf1-classmap</a>
</dd>
</dl>

<p>
I'm also posting a tarball here:
</p>

<dl>
<dd>
<a href="http://weierophinney.net/uploads/zf1-classmap.tgz">http://weierophinney.net/uploads/zf1-classmap.tgz</a>
</dd>
</dl>

<p>
The functionality includes:
</p>

<ul>
<li>
A class map generation tool
</li>
<li>
A <a href="https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md">PSR-0</a>-compliant autoloader, with <code>include_path</code> fallback capabilities
</li>
<li>
A class-map autoloader
</li>
<li>
An autoloader factory for loading many autoloading strategies at once
</li>
</ul>

<p>
I've included a README file that details most use cases:
</p>

<dl>
<dd>
<a href="https://github.com/weierophinney/zf-examples/blob/feature%2Fzf1-classmap/zf1-classmap/README.md">https://github.com/weierophinney/zf-examples/blob/feature%2Fzf1-classmap/zf1-classmap/README.md</a> 
</dd>
</dl>

<p>
The most interesting use case, I find, is combining a class-map autoloader with
a PSR-0 autoloader configured with one or more paths and set as a fallback. This
allows the benefits of performance when the class-map is seeded well, and
developer performance when in active development. For it to work, you need to
create at least an empty class-map. I will do the following immediately after
generating a project in order to pre-seed it:
</p>

<div class="example"><pre><code lang="bash">
prompt&gt; cd application/
prompt&gt; php /path/to/zf/bin/classmap_generator.php -w
  Creating class file map for library in '/var/www/project/application'...
  Wrote classmap file to '/var/www/project/application/.classmap.php'
prompt&gt; cd ../library/
prompt&gt; php /path/to/zf/bin/classmap_generator.php -w
  Creating class file map for library in '/var/www/project/library'...
  Wrote classmap file to '/var/www/project/library/.classmap.php'
</code></pre></div>

<p>
(The "-w" switch tells the generator to overwrite any classmap files already
present.)
</p>

<p>
From there, I do the following in <code>public/index.php</code>:
</p>

<div class="example"><pre><code lang="php">
require_once __DIR__ . '/../library/ZendX/Loader/AutoloaderFactory.php';
ZendX_Loader_AutoloaderFactory::factory(array(
    'ZendX_Loader_ClassMapAutoloader' =&gt; array(
        __DIR__ . '/../library/.classmap.php',
        __DIR__ . '/../application/.classmap.php',
    ),
    'ZendX_Loader_StandardAutoloader' =&gt; array(
        'prefixes' =&gt; array(
            'My' =&gt; __DIR__ . '/../library/My',
        ),
        'fallback_autoloader' =&gt; true,
    ),
));
</code></pre></div>

<p>
The above examples do the following:
</p>

<ul>
<li>
Create classmaps from the classes available in each of my "application" and
   "library" directories of my project.
</li>
<li>
Instantiates a class-map autoloader from those classmaps, and registers them
   with the SPL autoloader.
</li>
<li>
Creates a StandardAutoloader instance that's aware of the "My" vendor
   prefix, pointing to the "My" subdirectory in my library; as I add class files
   here, they will automatically be found.
</li>
<li>
Sets a fallback autoloader aware of my <code>include_path</code>.
</li>
</ul>

<p>
This setup takes a minute or so to create, but ensures that I'm immediately
productive. I then periodically update my classmap, by rerunning the
<code>classmap_generator.php</code> script on my application and library directories and
checking this in under version control.
</p>

<p>
This library is an excellent way to start boosting your ZF1 application
performance (particularly if you
<a href="http://framework.zend.com/manual/en/performance.classloading.html">strip your <code>require_once</code> calls</a>), while
simultaneously starting to make your code forward-compatible with ZF2.
</p>

<h2>Updates</h2>
<dl>
    <dt><b>2011-05-11 11:00 CDT</b></dt>
    <dd>Updated <code>classmap_generator.php</code> in the repository to remove a closure and thus make it truly PHP 5.2 compliant. Additionally, updated the <code>zf1-classmap.tgz</code> tarball with this change.</dd>

    <dt><b>2011-05-11 16:00 CDT</b></dt>
    <dd>Updated <code>ClassFileLocator</code> to define PHP 5.3-specific tokenizer constants when in earlier PHP versions.</dd>

    <dt><b>2011-05-23 10:25 CDT</b></dt>
    <dd>Updated <code>generate_classmap.php</code> to (a) use <code>DIRECTORY_SEPARATOR</code> in paths to ensure portability from Windows to *nix environments, and (b) cache the results of <code>dirname(__FILE__)</code> to improve performance. <it>Thanks to Tomas Fejfar for reporting these issues</it>.</dd>
</dl>
EOT;
$entry->setExtended($extended);

return $entry;