<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('188-Proper-Layer-files-when-using-Dojo-with-Zend-Framework');
$entry->setTitle('Proper Layer files when using Dojo with Zend Framework');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1220621400);
$entry->setUpdated(1220879854);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'dojo',
  1 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    During my <a href="http://www.zend.com/en/resources/webinars/framework">Dojo
        and ZF webinar</a> on Wednesday, 
    <a href="http://higginsforpresident.net/">Pete Higgins</a> of 
    <a href="http://dojotoolkit.org/">Dojo</a> fame noted that I could do
    something different and better on one of my slides.
</p>

<p>
    This particular item had to do with how I was consuming custom Dojo build
    layers within my code. I contacted him afterwards to find out what he
    suggested, and did a little playing of my own, and discovered some more Dojo
    and javascript beauty in the process.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    The code in question looked like this:
</p>

<div class="example"><pre><code lang="php">
Zend_Dojo::enableView($view);
$view-&gt;dojo()-&gt;setDjConfigOption('usePlainJson', true)
             // -&gt;setDjConfigOption('isDebug', true)
             -&gt;addStylesheetModule('dijit.themes.tundra')
             -&gt;addStylesheet('/js/dojox/grid/_grid/tundraGrid.css')
             -&gt;setLocalPath('/js/dojo/dojo.js')
             -&gt;addLayer('/js/paste/main.js')
             // -&gt;addLayer('/js/paste/paste.js')
             -&gt;registerModulePath('../paste', 'paste')
             -&gt;addJavascript('paste.main.init();')
             -&gt;disable();
</code></pre></div>

<p>
    The lines he was commenting onwere the <code>addLayer()</code> lines.
</p>

<p>
    As noted in my webinar, layers, or custom builds, are a fantastic feature of
    Dojo. Dojo is incredibly modular, and acts in many ways like a good
    server-side library should -- only include what is needed, and when its
    needed. The problem comes at deployment: the user suddenly experiences a
    situation where the application is making dozens of requests back to the
    server to get what it needs. The solution is to create a custom build, which
    pulls in all dependencies into a single file, inters any templates, and then
    does minification heuristics on the code prior to stripping all whitespace
    and compressing it. Once done, you now have a single, small file that needs
    to load on the request -- making the final deployed application snappy.
</p>

<p>
    When I displayed this during the webinar, I noted that after doing so, you
    have to change your code to point at the new build -- and that's what the
    two lines I pointed out are for. In essence, one is for development, the
    other for production. Of course, this is just ripe for problems -- you
    forget to switch comments in production, or accidently re-merge the
    development version, etc.
</p>

<p>
    Pete showed me another solution that was much more elegant, and which also
    got rid of another line in that solution above, the
    <code>addJavascript()</code call.
</p>

<p>
    The solution is to write your code in the same layer file as you'll compile
    to. When doing so, you can put all your <code>dojo.require()</code>
    statements in the file, as well as mixin any code you want in the main
    module namespace:
</p>

<div class="example"><pre><code lang="javascript">
dojo.provide(\&quot;paste.layer\&quot;);

/* Dojo modules to require... */
dojo.require(\&quot;dijit.layout.ContentPane\&quot;);
/* ... */

/* onLoad actions to perform... */
dojo.addOnLoad(function() {
    paste.upgrade(); 
});

/* mixin functionality to the \&quot;paste\&quot; namespace: */
dojo.mixin(paste, {
    /* paste.newPasteButton() */
    newPasteButton:  function() {
        var form = dijit.byId(\&quot;pasteform\&quot;);
        if (form.isValid()) {
            form.submit(); 
        }
    },
    
    /* ... */
});
</code></pre></div>

<p>
    In my original code, I had a "paste.main.init" method that performed all
    my <code>dojo.require</code> and <code>dojo.addOnLoad</code> statements, but
    these now can be simply a part of the layer -- eliminating more work for me.
</p>

<p>
    Then, when creating the profile, you simply have it create the layer in the
    same file -- in this case, paste/layer.js -- but also have it create a
    <em>dependency</em> on the original layer file. The compiler will ensure
    that the original code gets slurped into the build. As an example:
</p>

<div class="example"><pre><code lang="javascript">
dependencies = {
    layers: [
        {
            name: \&quot;../paste/layer.js\&quot;,
            dependencies: [
                \&quot;paste.layer\&quot;,
                /* other dependencies...*/
            ]
        },
    ],
    prefixes: [
        [ \&quot;paste\&quot;, \&quot;../paste\&quot; ],
        /* other prefixes -- dijit, etc. */
    ]
}
</code></pre></div>

<p>
    This changes the original ZF snippet above to simply:
</p>

<div class="example"><pre><code lang="php">
Zend_Dojo::enableView($view);
$view-&gt;dojo()-&gt;setDjConfigOption('usePlainJson', true)
             // -&gt;setDjConfigOption('isDebug', true)
             -&gt;addStylesheetModule('dijit.themes.tundra')
             -&gt;addStylesheet('/js/dojox/grid/_grid/tundraGrid.css')
             -&gt;setLocalPath('/js/dojo/dojo.js')
             -&gt;addLayer('/js/paste/layer.js')
             -&gt;registerModulePath('../paste', 'paste')
             -&gt;disable();
</code></pre></div>

<p>
    Not much shorter -- but because I no longer need to worry about changing the
    file name, I can rest easier at night.
</p>

<p>
    I'll be blogging more tips such as these in the coming weeks, to help
    support the new <a href="http://framework.zend.com/announcements/2008-09-03-dojo">Dojo integration</a> 
    in Zend Framework.
</p>
EOT;
$entry->setExtended($extended);

return $entry;