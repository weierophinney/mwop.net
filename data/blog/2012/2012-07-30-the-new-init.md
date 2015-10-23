---
id: 2012-07-30-the-new-init
author: matthew
title: 'ZF2''s New Controller::init()'
draft: false
public: true
created: '2012-07-30T15:40:00-05:00'
updated: '2012-07-30T21:00:00-05:00'
tags:
    - php
    - zf2
    - 'zend framework'
---
In Zend Framework 1, controller's had an `init()` method, which was called
after the controller was instantiated. The reason for it was to encourage
developers not to override the constructor, and thus potentially break some of
the functionality (as a number of objects were injected via the constructor).
`init()` was useful for doing additional object initialization.

```php
class MyController extends Zend_Controller_Action
{
    public function init()
    {
        // do some stuff!
    }
}
```

But this feature is missing from ZF2; how can we accomplish this sort of pattern?

<!--- EXTENDED -->

Constructor Overriding
----------------------

Why didn't we add the `init()` method in Zend Framework 2? Because we don't
have a constructor by default, and also to reduce overall method calls; if a
controller won't be overriding the method, there's no reason to make the call
in the first place. So, that means, in ZF2, unlike ZF1, to do the same thing,
you can simply define a constructor in your controller:

```php
use Zend\Mvc\Controller\AbstractActionController;

class MyController extends AbstractActionController
{
    public function __construct()
    {
        // do some stuff!
    }
}
```

Except there's one specific and often-needed use case where this fails: if you
want to wire listeners to the controller's event manager.

Events
------

Why does this fail with the event manager? Because when we're in the
constructor, we don't yet have an `EventManager` instance! The event manager
instance is injected after instantiation. As such, we need to attach to it once
we know we have an event manager. Which is… when it's set. This can be done
very simply by overriding the `setEventManager()` method. In the next example,
we'll define a listener for the "dispatch" event that redirects if certain
criteria is not met.

```php
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
```

The beauty of this is that we only override when we need to, and we override in
the specific context we're trying to influence. Additionally, we're only
registering the event listener if this particular controller is instantiated —
which helps reduce our overall number of listeners, and thus shapes our call
graph based on the controller invoked.

Other dependencies
------------------

As you'll likely note, the event manager is not the only case where you may
need to follow a similar pattern. Any time your initialization logic may be
based on a dependency, you'll want to override the setter in which that
dependency is injected.

*Got any ZF2 tips of your own to share? Blog them!*

Update: ServiceManager
----------------------

A few folks in the comments were remarking that the felt that omitting the
`init()` method makes it harder for developers to identify when and where to do
initialization logic, particularly when you may be working with multiple
dependencies.

Which made me realize there's another place I missed, one that's potentially
even better suited to initialization: the `ServiceManager`.

Basically, if you find yourself having complex initialization needs, or many
dependencies, you should be building a factory for your controller, and wiring
it to the `ServiceManager`. This can happen in one of several places, but my
preference is in my module's `Module` class, in the `getControllerConfig()`
method. This method returns configuration for the controller manager that
instantiates, validate, and injects controllers; it's basically a type of
`ServiceManager`, and, in fact, has access to the main application's instance.
I'll take the previous example, and wire it in the context of a factory:

```php
namespace My

use Zend\EventManager\EventManagerInterface;

class Module
{
    /*
     * Assume some other methods, such as getConfig(), etc.
     * Also assume that a route will return a controller named
     * "My\Controller\My" which we assume will reference a controller
     * within our current namespace.
     */

    public function getControllerConfig()
    {
        return array('factories' => array(
            'My\Controller\My' => function ($controllers) {
                $services   = $controllers->getServiceLocator();
                $controller = new Controller\MyController();
                $events     = $services->get('EventManager')

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

                $controller->setEventManager($events);
                return $controller;
            };
        ));
    }
}
```

The above will create the controller, grab an event manager instance, attach
the listener, and then inject the event manager into the controller. If you
wanted to do more complex work, you definitely could — and this would be the
place to do it.
