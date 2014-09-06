<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('139-PHP-globals-for-the-OOP-developer');
$entry->setTitle('PHP globals for the OOP developer');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1179582168);
$entry->setUpdated(1179680294);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    <b>Update:</b> I evidently simplified the issue too much, and have had several
    people rightly comment on the bogosity of the issue. However, there are still situations
    where <kbd>$GLOBALS</kbd> does not act as expected, and I outline these in
    <a href="http://weierophinney.net/matthew/archives/140-Globals,-continued.html">my next entry</a>.
</p>
<hr />
<p>
    In my <a
        href="/matthew/archives/138-Start-Writing-Embeddable-Applications.html">previous
        entry</a>, I ranted about the use of globals in popular PHP
    applications, and how they make embedding said applications difficult. I
    develop using object-oriented practices, and can honestly say I can't recall
    ever having slung a global variable around in my own code. Globals seem
    hackish to me, and as a result, trying to get applications that use them
    to behave correctly has been a challenge.
</p>

<p>
    One of the applications I had in mind was <a
        href="http://www.s9y.org">Serendipity</a>, the software that powers this
    blog. I was attempting to create a Zend Framework action controller that
    wraps my s9y instance so that I can do things such as apply ACLs from my
    website to selected entries, as well as pull the sitewide skeleton out from
    s9y so that I only have to maintain one version of it (I had one version for
    s9y, and another for my own content featured on the site (resume, contact
    form, etc.).
</p>

<p>
    I tried importing the various config files into my action method prior to
    invoking the actual s9y bootstrap, but no dice. I also tried modifying the
    s9y config files to use the notation <kbd>$GLOBALS['serendipity']</kbd>
    around the serendipity configuration variables (s9y uses a single
    multi-dimensional array for all configuration options). This didn't work,
    either; s9y functions that called <kbd>global $serendipity</kbd> were still
    getting a null value.
</p>

<p>
    So, I did a little closer reading in the manual <a
        href="http://php.net/language.variables.predefined">section on
        predefined variables</a>, I discovered something interesting in the
    description of <kbd>$GLOBALS</kbd> (emphasis mine):
</p>

<blockquote>
    Contains a reference to every variable which is <em>currently</em> available
    within the global scope of the script.
</blockquote>

<p>
    Interestingly, the section on variable scope didn't make this distinction at
    all. Basically, if the variable you reference via <kbd>$GLOBALS</kbd> does
    not already exist, assigning it <em>does nothing</em>. It doesn't even raise
    a notice. It just silently goes ahead, leaving you thinking you set a new
    global variable, but in fact, you cannot assign new globals via
    <kbd>$GLOBALS</kbd>; you can only modify <em>existing</em> variables in the
    global scope.
</p>

<p>
    So, I got around the issue by putting this in my front controller bootstrap:
</p>

<div class="example"><pre><code lang="php">
$serendipity = null;
</code></pre></div>

<p>
    After that, I was able to create a wrapper action controller for s9y very
    easily:
</p>

<div class="example"><pre><code lang="php">
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Serendipity integration
 * 
 * @uses       Zend_Controller_Action
  */
class S9y_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        // New ViewRenderer helper in ZF incubator; telling it not
        // to autorender a view script when done
        $this-&gt;_helper-&gt;viewRenderer-&gt;initView(null, null, array('noRender' =&gt; true));
    }

    public function indexAction()
    {
        global $serendipity;
        chdir($_SERVER['DOCUMENT_ROOT'] . '/path/to/s9y');
        include './index.php';
        chdir($_SERVER['DOCUMENT_ROOT']);
    }
}
</code></pre></div>

<p>
    Note that I don't do any output buffering; this is because the ZF dispatcher
    takes care of that for me. All I need to do is execute the s9y bootstrap.
</p>

<p>
    So, the lesson to learn from all this: if you need to wrap an application
    that uses globals, find out what all of them are, and declare them in the
    global namespace -- just setting them to null is enough -- in your
    application bootstrap.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;