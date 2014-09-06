<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('135-Extending-Singletons');
$entry->setTitle('Extending Singletons');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1170687841);
$entry->setUpdated(1171175398);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    This morning, I was wondering about how to extend a singleton class such
    that you could retrieve the new class when retrieving the singleton later.
    In particular, Zend_Controller_Front is a singleton, but what if I want to
    extend it later? A number of plugins in the Zend Framework, particularly
    view helpers and routing functionality, make use of the singleton; would I
    need to alter all of these later so I could make use of the new subclass?
</p>
<p>
    For instance, try the following code:
</p>
<div class="example"><pre><code lang="php">
class My_Controller_Front extends Zend_Controller_Front
{}

$front = My_Controller_Front::getInstance();
</code></pre></div>
<p>
    You'll get an instance of Zend_Controller_Front. But if you do the
    following:
</p>
<div class="example"><pre><code lang="php">
class My_Controller_Front extends Zend_Controller_Front
{
    protected static $_instance;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}

$front = My_Controller_Front::getInstance();
</code></pre></div>
<p>
    You'll now get an instance of My_Controller_Front. However, since
    <kbd>$_instance</kbd> is <em>private</em> in Zend_Controller_Front, calling
    <kbd>Zend_Controller_Front::getInstance()</kbd> will still return a
    Zend_Controller_Front instance -- not good.
</p>
<p>
    However, if I redefine <kbd>Zend_Controller_Front::$_instance</kbd> as
    <em>protected</em>, and have the following:
</p>
<div class="example"><pre><code lang="php">
class My_Controller_Front extends Zend_Controller_Front
{
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}

$front = My_Controller_Front::getInstance();
</code></pre></div>
<p>
    Then the any time I call <kbd>getInstance()</kbd> on either
    My_Controller_Front or Zend_Controller_Front, I get a My_Controller_Front
    instance!
</p>
<p>
    So, the takeaway is: if you think a singleton object could ever benefit from
    extension, define the static property holding the instance as protected, and
    then, in any extending class, override the method retrieving the instance.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;