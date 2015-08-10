<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('208-Recursively-Destroying-Dojo-ContentPanes');
$entry->setTitle('Recursively Destroying Dojo ContentPanes');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1235395020);
$entry->setUpdated(1235395020);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'dojo',
));

$body =<<<'EOT'
<p>
    I ran into an issue recently with Dojo's ContentPanes. I was using them with
    a TabContainer, and had made them closable; however, user actions might
    re-open tabs that pull from the same source. This led to conflicts with
    dijit IDs that I had to resolve.
</p>

<p>
    Most Dijits have a <code>destroyRecursive()</code> method which should,
    theoretically, destroy any dijits contained within them. However, for many
    Dijits, this functionality simply does not work due to how they are
    implemented; many do not actually have any knowledge of the dijits beneath
    them.
</p>

<p>
    ContentPanes fall into this latter category. fortunately, it's relatively
    easy to accomplish, due to Dojo's heavily object oriented nature.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<div class="example"><pre><code class="language-javascript">
dojo.provide(\&quot;custom.ContentPane\&quot;);

dojo.require(\&quot;dijit.layout.ContentPane\&quot;);

dojo.declare(\&quot;custom.ContentPane\&quot;, [dijit.layout.ContentPane], {
    postMixInProperties: function(){
        if (dijit.byId(this.id)) {
            dijit.byId(this.id).destroyRecursive();
        }
    },

    destroyRecursive: function(){
        dojo.forEach(this.getDescendants(), function(widget){
            widget.destroyRecursive();
        });
        this.inherited(arguments);
    }
});
</code></pre></div>

<p>
    The <code>destroyRecursive()</code> method is not that different from the
    one in <code>dijit._Widget</code>; the difference is that instead of calling
    simply <code>destroy()</code> on any discovered widgets, we destroy
    recursively. 
</p>

<p>
    The <code>postMixInProperties</code> method I added due to IE issues I've
    run into. Basically, even though the ContentPane was being destroyed
    recursively, for some reason IE was keeping a reference to the original
    dijit floating around. <code>postMixInProperties()</code> checks to see if
    the dijit ID is still around, and if so, destroys it recursively. This
    allows the ContentPane initialization to proceed.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
