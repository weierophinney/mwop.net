<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('243-Running-mod_php-and-FastCGI-side-by-side');
$entry->setTitle('Running mod_php and FastCGI side-by-side');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1281365100);
$entry->setUpdated(1281879629);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'fastcgi',
));

$body =<<<'EOT'
<p>
    Because we're in full throes of <a href="http://framework.zend.com">Zend
        Framework</a> 2.0 development, I find myself with a variety of PHP
    binaries floating around my system from both the PHP 5.2 and 5.3 release
    series. We're at a point now where I'm wanting to test migrating
    applications from ZF 1.X to 2.0 to se see what works and what doesn't. But
    that means I need more than one PHP binary enabled on my server...
</p>

<p>
    I use <a href="http://www.zend.com/products/server/">Zend Server</a> on my
    development box; it's easy to install, and uses my native Ubuntu update
    manager to get updates. On Ubuntu, it installs the Debian Apache2 packages,
    so I get the added bonus of familiarity with the configuration structure.
</p>

<p>
    I installed Zend Server some time ago, so I'm still on a PHP 5.2 mod_php
    binary. I have several PHP 5.3 binaries compiled and installed locally for
    running unit tests and sample scripts already -- so the question was how to
    keep my 5.2 mod_php running while simultaneously allowing the ability to run
    selected vhosts in 5.3?
</p>

<p>
    The answer can be summed up in one acronym: FastCGI.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    With a little help from <a href="http://ralphschindler.com">Ralph
        Schindler</a>, I got things setup.
</p>

<h2>Enabling FastCGI on Ubuntu's Apache</h2>

<p>
    Interestingly, FastCGI is not enabled by default, nor is another module
    you'll need, <code>mod_actions</code>. You can enable these very easily
    though:
</p>

<div class="example"><pre><code class="language-bash">
% cd /etc/apache2/mods-enabled
% sudo ln -s ../mods-available/fastcgi.load .
% sudo ln -s ../mods-available/fastcgi.conf .
% sudo ln -s ../mods-available/actions.load .
% sudo ln -s ../mods-available/actions.conf .
</code></pre></div>

<h2>Create a FastCGI-enabled vhost</h2>

<p>
    Next, you need to add a new vhost that will utilize FastCGI. I copied an
    existing vhost I had in my <code>/etc/apache2/sites-enabled</code> tree,
    modified it to give it a unique <code>ServerName</code> and
    <code>DocumentRoot</code>, and added the following lines:
</p>

<div class="example"><pre><code class="language-apache">
ScriptAlias /cgi-bin/ /path/to/zfproject/public/cgi-bin/
AddHandler php-fcgi .php
Action php-fcgi /cgi-bin/php-5.3.1
</code></pre></div>

<p>
    The name of the PHP script doesn't matter much; I used "php-5.3.1" so that I
    could visually recognize what version of PHP I was using with that vhost.
</p>

<h2>Create a "cgi-bin" directory and CGI script</h2>

<p>
    Finally, I needed to actually create the "cgi-bin" directory and CGI script
    to execute. This was relatively simple; I navigated to my project's
    <code>DocumentRoot</code>, and created a new directory "cgi-bin"
    (<code>mkdir cgi-bin</code>).
</p>

<p>
    I then entered that directory and created a new script, based on the name I
    provided in my vhost. That script, "cgi-bin/php-5.3.1" then simply
    <code>exec</code>'s the <code>php-cgi</code> binary from my PHP install.
</p>

<div class="note" style="border: 1px solid black; padding: 2px;">
    <h3>Note about CGI binaries</h3>

    <p>
        In PHP 5.3 and up, CGI binaries are built by default -- and they're
        already FastCGI enabled. In PHP 5.2, CGI versions are still built by
        default, but they are not FastCGI-enabled unless you explicitly pass the
        "--enable-fastcgi" configure flag. To determine if you did that when
        compiling, execute the following:
    </p>

<div class="example"><pre><code class="language-bash">
php-cgi -i | grep fcgi
</code></pre></div>

    <p>
        If you get no output, you need to recompile.
    </p>
</div>

<p>
    My script looks like this:
</p>

<div class="example"><pre><code class="language-bash">
#!/bin/bash
exec /path/to/php/install/bin/php-cgi \&quot;$@\&quot;
</code></pre></div>

<p>
    Because this is a CGI binary, you can pass additional CLI arguments and
    environment variables; try experimenting with setting your
    <code>include_path</code>, application environment, etc.
</p>

<p>
    Once you're done creating the script, make sure it's executable:
</p>

<div class="example"><pre><code class="language-sh">
chmod a+x php-5.3.1
</code></pre></div>

<h2>Fire it up!</h2>

<p>
    Once I'd done the above, I restarted my Apache instance (<code>sudo
    /etc/init.d/apache2 restart</code>). After ensuring there were no startup
    errors, I navigated to my new vhost, and <i>voila!</i> it was running.
</p>

<p>
    For those of you doing your first forays into PHP 5.3, this is an excellent
    way to test code without needing a separate server running. It's also a
    great way to test whether your application is 5.3-ready -- create a
    5.3-enabled vhost pointing to your existing application and see if it runs.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
