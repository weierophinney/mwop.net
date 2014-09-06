<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('206-Zend-Framework-1.7.5-Released-Important-Note-Regarding-Zend_View');
$entry->setTitle('Zend Framework 1.7.5 Released - Important Note Regarding Zend_View');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1234888398);
$entry->setUpdated(1235393050);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
));

$body =<<<'EOT'
<p>
    Yesterday, we released 
    <a href="http://framework.zend.com/download/latest">Zend Framework 1.7.5</a>. 
    It contains a somewhat controversial security fix to Zend_View that could
    potentially affect some use cases of the component; I'm providing details on
    that security fix as well as how to work around it here.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    A user filed an issue report showing a potential Local File Inclusion
    vulnerability in Zend_View's <code>setScriptPath()</code> method: if user
    input were used to specify the script path, then it was possible to trigger
    the LFI. The vulnerability was completely contrived; no sane developer
    should ever configure the view script paths using user input. However, it
    pointed out another very real LFI attack vector.
</p>

<p>
    The attack vector is, once again, a situation of trusting unfiltered input,
    but this time it's a much more likely scenario. In this particular case,
    let's say we had Zend_View configured as follows:
</p>

<div class="example"><pre><code lang="php">
$view-&gt;addScriptPath('/var/www/application/views/scripts');
</code></pre></div>

<p>
    We then accepted the following input, and passed it to the
    <code>render()</code> method: "../../../../etc/passwd".
</p>

<p>
    The LFI vector was then triggered, as <code>render()</code> actually allowed
    for parent directory traversal.
</p>

<p>
    ZF 1.7.5 now has a check for such notation ('../' or '..\'), and throws an
    exception when detected.
</p>

<p>
    On #zftalk.dev, several contributors noted that this could potentially break
    some of their applications. In their situations, they were using parent
    directory traversal, but not from user input. In such a situation, since
    they have control over the value, they felt the check was better left to
    userland code.
</p>

<p>
    To accomodate this, we introduced a flag, "lfiProtectionOn". By default,
    this flag is true, enabling the check. You may turn it off in one of two
    ways:
</p>

<div class="example"><pre><code lang="php">
// At instantiation:
$view = new Zend_View(array(
    'lfiProtectionOn' =&gt; false,
));

// Programmatically, at any time:
$view-&gt;setLfiProtection(false);
</code></pre></div>

<p>
    Including this security fix was a hard decision. On the one hand, we try
    very hard to keep backwards compatibility between versions. On the other,
    there's also a very real responsibility to our users to keep them secure.
    Hopefully, the addition of the LFI protection flag above will help ease the
    migration issues.
</p>

<p>
    For more information on this change, you can <a
        href="http://framework.zend.com/manual/en/zend.view.migration.html">read the
        relevant manual page</a>.
</p>
EOT;
$entry->setExtended($extended);

return $entry;