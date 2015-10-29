---
id: 234-Module-Bootstraps-in-Zend-Framework-Dos-and-Donts
author: matthew
title: 'Module Bootstraps in Zend Framework: Do''s and Don''ts'
draft: false
public: true
created: '2010-03-11T11:55:55-05:00'
updated: '2010-03-15T20:31:53-04:00'
tags:
    0: php
    2: 'zend framework'
---
I see a number of questions regularly about module bootstraps in Zend
Framework, and decided it was time to write a post about them finally.

In Zend Framework 1.8.0, we added `Zend_Application`, which is intended to (a)
formalize the bootstrapping process, and (b) make it re-usable. One aspect of
it was to allow bootstrapping of individual application modules — which are
discrete collections of controllers, views, and models.

The most common question I get regarding module bootstraps is:

> Why are all module bootstraps run on every request, and not just the one for the requested module?

To answer that question, first I need to provide some background.

<!--- EXTENDED -->

When it comes to modules, we have three typical problems or requirements:

- Ensuring that module resources — models, view helpers, etc. — are available elsewhere in the application
- Initializing module-specific resources, such as routes, navigation elements, etc.
- Running code specific to this module (selecting a specific layout, selecting a specific database adapter, etc)

`Zend_Application` answers the first two questions. By default, it sets up a
resource autoloader with targets for all the common resources (models, forms,
view helpers and filters, DbTable objects, etc.), and also allows you to
specify resources to load at bootstrap time.

And that's where things get interesting.

The basic workflow of a ZF MVC request is as follows:

1. Application bootstrapping
2. Routing
3. Dispatch

`Zend_Application` takes care of only the first item in that list,
bootstrapping. At that time, we have no idea what the request actually is —
that happens during routing. It's only after we have routed that we know what
module, controller, and action were requested.

So, what's the point of your module bootstraps, then?

Bootstrapping is for getting ready
----------------------------------

As noted earlier, `Zend_Application` is intended for bootstrapping your
application. This means "getting it ready to execute". The idea is to get all
your dependencies in order so that once you're ready to route and/or dispatch,
everything the application may need is in place.

When it comes to modules, the sorts of things you need to have in place *before* routing and dispatch include:

- Autoloading support for module resources. This is so that, if you need to,
  code from anywhere in your application can make uses of the module's
  resources. Examples include access to view helpers, access to models, access
  to forms, etc. Autoloading of resources is enabled by default
- Setting up module-specific routes. How can you get to the module's
  controllers in the first place? What routes does it answer to? The time to
  provide this information is during bootstrapping, *before* routing occurs.
- Module-specific navigation elements. This usually goes hand-in-hand with your
  routes (most `Zend_Navigation` pages utilize named routes).
- Setting up module-specific plugins. If there is functionality your module may
  be needing to enable as part of the routing/dispatch cycle, set this up in
  plugins and attach them to the front controller.

This last point is the key to understanding the appropriate place to do
module-specific initializations — that is, initialization and/or bootstrapping
that should only be done if the module is matched during routing.

Use plugins to do specific initializations
------------------------------------------

To re-iterate: if you have initialization tasks that should only be done if the
module is the one being executed, do it in a front controller plugin or action
helper.

If doing it in a front controller plugin, do these initializations any time
after routing, as this is the only time you'll know what the module is. For
general tasks like switching the layout, `routeShutdown()` or
`dispatchLoopStartup()` are the right places. Simply compare the module in the
request object to your module, and bail early if they don't match.

```php
class Foomodule_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if ('foomodule' != $request->getModuleName()) {
            // If not in this module, return early
            return;
        }

        // Change layout
        Zend_Layout::getMvcInstance()->setLayout('foomodule');
    }
}
```

Your module *bootstrap* would take care of registering this plugin with the front controller:

```php
class Foomodule_Boootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initPlugins()
    {
        $bootstrap = $this->getApplication();
        $bootstrap->bootstrap('frontcontroller');
        $front = $bootstrap->getResource('frontcontroller');

        $front->registerPlugin(new Foomodule_Plugin_Layout());
    }
}
```

To keep things simple, and to reduce the performance overhead of having a lot
of plugins, you might want to create a single plugin that performs all
initialization; the Facade pattern is a good one to use here.

If using action helpers, the idea is the same — the only difference is that you
register with the action helper broker, and will likely do your matching in a
`preDispatch()` hook.

Isn't there a better way to do this?
------------------------------------

Yes, likely there are better ways to accomplish this. The true problem is that
modules are really second-class citizens in ZF currently. There are a few neat
ideas floating around:

- [Kathryn's Active module config](http://binarykitten.me.uk/dev/zend-framework/177-active-module-based-config-with-zend-framework.html)
- [Jeroen's Moduleconfig](http://www.amazium.com/blog/zend-framework-module-specific-config)
- [Matthijs' ModuleConfig](http://blog.vandenbos.org/2009/07/07/zend-framework-module-config/)
- [Pádraic and Rob's Module Configurators proposal](http://framework.zend.com/wiki/pages/viewpage.action?pageId=16023853)

For 2.0, we'll be analyzing the situation and seeing if we can come up with a
way to make module's first-class citizens in ZF. My hope is that this will
allow users to start sharing modules easily — which can foster a more
"plugin"-like approach to building websites, and lead to collaboration on
oft-needed site functionality (such as modules for blog, news, contact, etc.).

In the meantime, hopefully this post has helped shed some light on how module
configuration currently works, and provides some tips and techniques on how to
setup your application to make use of module-specific resources and
initialization.

#### Updates

- 2010-03-12: added link to Paddy's proposal
