---
id: 235-A-Simple-Resource-Injector-for-ZF-Action-Controllers
author: matthew
title: 'A Simple Resource Injector for ZF Action Controllers'
draft: false
public: true
created: '2010-03-19T16:20:42-04:00'
updated: '2010-03-23T16:59:54-04:00'
tags:
    0: php
    2: 'zend framework'
---
[Brandon Savage](http://www.brandonsavage.net/) approached me with an
interesting issue regarding ZF bootstrap resources, and accessing them in your
action controllers. Basically, he'd like to see any resource initialized by the
bootstrap immediately available as simply a public member of his action
controller.

So, for instance, if you were using the "DB" resource in your application, your
controller could access it via `$this->db`.

<!--- EXTENDED -->

I quickly drafted up a proof of concept for him using an action helper:

```php
class My_ResourceInjector extends Zend_Controller_Action_Helper_Abstract
{
    protected $_resources;

    public function __construct(array $resources = array())
    {
        $this->_resources = $resources;
    }
 
    public function preDispatch()
    {
        $bootstrap  = $this->getBootstrap();
        $controller = $this->getActionController();
        foreach ($this->_resources as $name) {
            if ($bootstrap->hasResource($name)) {
                $controller->$name = $bootstrap->getResource($name);
            }
        }
    }
 
    public function getBootstrap()
    {
        return $this->getFrontController()->getParam('bootstrap');
    }
}
```

In this action helper, you would specify the specific resources you want
injected via the `$_resources` property - which would be values you pass in.
Each resource name would then be checked against those available in the
bootstrap, and, if found, injected into the action controller as a property of
the same name.

You would initialize it in your bootstrap:

```php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initResourceInjector()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new My_ResourceInjector(array(
                'db',
                'layout',
                'navigation',
            ));
        );
    }
}
```

The above would map three resources: "db", "layout", and "navigation". This
means you can refer to them directly as properties in your controllers:

```php
class FooController extends Zend_Controller_Action
{
    public function barAction()
    {
        $this->layout->disableLayout();
        $model = $this->getModel();
        $model->setDbAdapter($this->db);
        $this->view->assign(
            'model'      => $this->model,
            'navigation' => $this->navigation,
        );
    }

    // ...
}
```

This approach leads to some nice brevity — you no longer need to fetch the
bootstrap from the instantiation arguments, and then fetch the resource.

I thought about it some more, and realized that there's a few problems: How do
you know what is being injected from within the controller? How do you control
what is being injected.

So, I revised it to pull the expected dependencies from the action controller itself:

```php
class My_ResourceInjector extends Zend_Controller_Action_Helper_Abstract
{
    protected $_resources;

    public function preDispatch()
    {
        $bootstrap  = $this->getBootstrap();
        $controller = $this->getActionController();

        if (!isset($controller->dependencies) 
            || !is_array($controller->dependencies)
        ) {
            return;
        }

        foreach ($controller->dependencies as $name) {
            if ($bootstrap->hasResource($name)) {
                $controller->$name = $bootstrap->getResource($name);
            }
        }
    }
 
    public function getBootstrap()
    {
        return $this->getFrontController()->getParam('bootstrap');
    }
}
```

You would still register this in your bootstrap, but now you would no longer
need any constructor arguments:

```php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initResourceInjector()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new My_ResourceInjector();
        );
    }
}
```

Instead, you define the resources you need to retrieve in your controller:

```php
class FooController extends Zend_Controller_Action
{
    public $dependencies = array(
        'db',
        'layout',
        'navigation',
    );

    public function barAction()
    {
        $this->layout->disableLayout();
        $model = $this->getModel();
        $model->setDbAdapter($this->db);
        $this->view->assign(
            'model'      => $this->model,
            'navigation' => $this->navigation,
        );
    }

    // ...
}
```

This makes it far more clear what your dependencies are, and also ensures that
each controller only gets the dependencies it plans on using. However, I think
it can still be improved: if the dependency is not found, we should likely
throw an exception!

```php
class My_ResourceInjector extends Zend_Controller_Action_Helper_Abstract
{
    protected $_resources;

    public function preDispatch()
    {
        $bootstrap  = $this->getBootstrap();
        $controller = $this->getActionController();

        if (!isset($controller->dependencies) 
            || !is_array($controller->dependencies)
        ) {
            return;
        }

        foreach ($controller->dependencies as $name) {
            if (!$bootstrap->hasResource($name)) {
                throw new DomainException("Unable to find dependency by name '$name'");
            }
            $controller->$name = $bootstrap->getResource($name);
        }
    }
 
    public function getBootstrap()
    {
        return $this->getFrontController()->getParam('bootstrap');
    }
}
```

This better satisfies the goals and needs of dependency tracking. Dependencies
are defined by the object that needs them, they're injected by a collaborator,
and missing dependencies results in an exception.

One potential improvement would be to allow specifying "default" resources to
inject into all controllers; this could be accomplished with a constructor
argument similar to the second example provided, and merging that value with
the controller dependencies. I'll leave that as an exercise for the reader,
though.

Action helpers are an area that is largely unexplored by many ZF users.
Hopefully this post will show just how powerful they can be, and how much they
can automate common tasks.
