<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-07-30-the-new-init');
$entry->setTitle('ZF2\'s New Controller::init()');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-07-30 15:40', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-07-30 15:40', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array (
  'php',
  'zf2',
  'zend framework',
));

$body =<<<'EOT'
<p>
    In Zend Framework 1, controller's had an <code>init()</code> method, which
    was called after the controller was instantiated. The reason for it was to
    encourage developers not to override the constructor, and thus potentially
    break some of the functionality (as a number of objects were injected via
    the constructor). <code>init()</code> was useful for doing additional object
    initialization.
</p>

<div class="example"><pre><code language="php">
class MyController extends Zend_Controller_Action
{
    public function init()
    {
        // do some stuff!
    }
}
</code></pre></div>

<p>
    But this feature is missing from ZF2; how can we accomplish this sort of
    pattern?
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'

<h2>Constructor Overriding</h2>

<p>
    Why didn't we add the <code>init()</code> method in Zend Framework 2?
    Because we don't have a constructor by default, and also to reduce overall
    method calls; if a controller won't be overriding the method, there's no
    reason to make the call in the first place.  So, that means, in ZF2, unlike
    ZF1, to do the same thing, you can simply define a constructor in your
    controller:
</p>

<div class="example"><pre><code language="php">
use Zend\Mvc\Controller\AbstractActionController;

class MyController extends AbstractActionController
{
    public function __construct()
    {
        // do some stuff!
    }
}
</code></pre></div>

<p>
    Except there's one specific and often-needed use case where this fails: if
    you want to wire listeners to the controller's event manager.
</p>

<h2>Events</h2>

<p>
    Why does this fail with the event manager? Because when we're in the
    constructor, we don't yet have an <code>EventManager</code> instance!
    The event manager instance is injected after instantiation. As such, we
    need to attach to it once we know we have an event manager. Which is...
    when it's set. This can be done very simply by overriding the
    <code>setEventManager()</code> method. In the next example, we'll
    define a listener for the "dispatch" event that redirects if certain
    criteria is not met.
</p>


<div class="example"><pre><code language="php">
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class MyController extends AbstractActionController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $request = $e->getRequest();
            $method  = $request->getMethod();
            if (!in_array($method, array('PUT', 'DELETE', 'PATCH'))) {
                // nothing to do
                return;
            }

            if ($controller->params()->fromRoute('id', false)) {
                // nothing to do
                return;
            }

            // Missing identifier! Redirect.
            return $controller->redirect()->toRoute(/* ... */);
        }, 100); // execute before executing action logic
    }
}
</code></pre></div>

<p>
    The beauty of this is that we only override when we need to, and we override
    in the specific context we're trying to influence. Additionally, we're only
    registering the event listener if this particular controller is instantiated
    -- which helps reduce our overall number of listeners, and thus shapes our
    call graph based on the controller invoked.
</p>

<h2>Other dependencies</h2>

<p>
    As you'll likely note, the event manager is not the only case where you may
    need to follow a similar pattern. Any time your initialization logic may be
    based on a dependency, you'll want to override the setter in which that
    dependency is injected.
</p>

<p>
    <em>Got any ZF2 tips of your own to share? Blog them!</em>
</p>
EOT;
$entry->setExtended($extended);

return $entry;
