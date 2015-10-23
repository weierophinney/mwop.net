---
id: 117-Cgiapp2-Tutorial-1-Switch-Template-Plugins-at-Will
author: matthew
title: 'Cgiapp2 Tutorial 1: Switch Template Plugins at Will'
draft: false
public: true
created: '2006-06-05T22:32:03-04:00'
updated: '2006-06-05T23:01:48-04:00'
tags:
    - php
---
This is the first in a short series of tutorials showcasing some of the new features of [Cgiapp2](/phly/index.php?package=Cgiapp2). In this tutorial, you will see how easy it is to switch template engines in Cgiapp2-based applications.

<!--- EXTENDED -->

Cgiapp2 implements a new callback hook system, which is basically an [Observer pattern](http://en.wikipedia.org/wiki/Observer_pattern). Cgiapp2 has a number of registered hooks to which observers can attach; when a hook is triggered, each observer attached to it is notified and executed. Additionally, Cgiapp2 provides a means to create new hooks in your applications that others may observer; that's a subject for another post.

Why all this talk about hooks? Because in Cgiapp2, the various template actions — initialization, variable assignment, and rendering — are relegated to hooks. For simplicity's sake, and for backward compatibility, you can use the functions `tmpl_path()`, `tmpl_assign()`, and `load_tmpl()` to invoke them; you could also use the generic `call_hook()` method to do so, passing the hook name as the first argument.

To standardize template actions, I developed [Cgiapp2_Plugin_Template_Interface](/phly/darcs/annotate/cgiapp/Cgiapp2/Plugin/Template/Interface.class.php), a standard interface for template plugins. Any template plugin that implements this interface can be called with the standard `tmpl_*` methods — which means that developers can mix-and-match template engines at will!

Since Cgiapp2 and its subclasses no longer need to be aware of the rendering engine, developers that are instantiating Cgiapp2-based applications can choose their own rendering engine at the time of instantiation:

```php
<?php
require_once 'Some/Cgiapp2/Application.php';
require_once 'Cgiapp2/Plugin/Savant3.php';
$app = new Some_Cgiapp2_Application($options);
$app->run();
```

In the example above, developer X uses Savant3 as the template engine. Now, say you're developer Y, and have an affinity for Smarty, and want to use that engine for the application. No problem:

```php
<?php
require_once 'Some/Cgiapp2/Application.php';
require_once 'Cgiapp2/Plugin/Smarty.php';
$app = new Some_Cgiapp2_Application($options);
$app->run();
```

Now all you have to do is create Smarty versions of the templates. Cgiapp2 doesn't care to which engine it's rendering; it simply notifies the last registered template plugin.

Stay tuned for more tutorials in the coming days!
