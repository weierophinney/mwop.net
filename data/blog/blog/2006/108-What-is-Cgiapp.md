---
id: 108-What-is-Cgiapp
author: matthew
title: 'What is Cgiapp?'
draft: false
public: true
created: '2006-04-30T09:54:00-04:00'
updated: '2006-05-02T07:37:09-04:00'
tags:
    - php
---
After some conversations with [Paul](http://paul-m-jones.com/) and [Mike](http://naberezny.com/), in recent months I realized that while I often announce new releases of [Cgiapp](http://cgiapp.sourceforge.net/), I rarely explain what it is or why I develop it.

~~I got into trouble on the [PEAR](http://pear.php.net/) list when I tried to propose it for inclusion in that project, when I made the mistake of describing it as a framework. (This was before frameworks became all the rage on the PHP scene; PEAR developers, evidently, will not review anything that could possibly be construed or interpreted as a framework, even if it isn't.)~~ I mistakenly called Cgiapp a framework once when considering proposing it to PEAR. But if it's not a framework, what is Cgiapp? Stated simply:

> Cgiapp is the Controller of a Model-View-Controller (MVC) pattern. It can be either a front controller or an application controller, though it's typically used as the latter.

<!--- EXTENDED -->

As a controller, it provides some basic, configurable routing mechanisms to determine what should be displayed for a given request, a simple registry for configuration variables and for passing variables around between method calls, error handling, hooks into a template engine, and hooks into pre/post application and request operations.

When developing applications using Cgiapp, you operate on the idea of one display screen, one method. You define a hash table mapping display screens to methods, and either indicate a query parameter or a segment of the request URI that will indicate the display requested; when the request comes in, the appropriate method is called. In Cgiapp terms, these are called "run modes".

What makes Cgiapp so enticing for me as a developer?

- **Object orientation.** Cgiapp is inherently object oriented; to use it, you must create a class that extends Cgiapp (or Cgiapp2!), and map methods to actions. There's no getting around it. Because of this, applications are namespaced, portable, and testable.
- **Reusability.** Because each application is a class, and each application instance is triggered from an instance script, and because you may pass configuration variables to the class from the instance script, Cgiapp-based applications are inherently reusable. This makes it easy to distribute applications, as well as to reuse them in multiple site locations. As an example, I have used article applications in multiple locations, accessing different article stores -- but using the same article application class. Each had a different look and feel, sometimes within the same site, sometimes in different sites, but I merely needed to change the data store and the templates to achive the differentiation.
- **Extensibility.** Ever code something, and then need an application that duplicated much of the functionality, but added some twists to it? One such example: I had a small gallery application, and later needed an e-card application. The latter was basically a gallery type of application, but when selecting an image, needed to display an e-card form, and also needed to handle the results of that form. I simply had the e-card class extend the gallery class, added a method, and created a new view template for the image view. Total extra work: about an hour or so.
- **Developer freedom.** Because Cgiapp is *not* a framework, and, indeed, only a single class (that changes in Cgiapp2, but only because Cgiapp2 has some helper classes), it gives a lot of freedom to the developer. You can pick and choose what templating system you want to use. Or what data storage mechanism you want to use. Or how you'll handle sessions. Or whether you'll use pretty urls or GET parameters (or allow one, but default to the other). In a nutshell, it allows the developer to cherry pick the libraries and components they want to use in their application.

It's the last point, above, that really separates Cgiapp from most other MVC frameworks I've reviewed. Most frameworks tend to give you everything, package it up, and try to sell it to you as the developer: "Use only the tools integrated in our solution, we can't guarantee best performance otherwise." Cgiapp doesn't do that. Cgiapp let's the developer call the shots and say, "I'm familiar with such-and-such library and want to use that in my MVC application," or, "I like such-and-such template engine, and want to use that," or, "I need my application to work regardless of whether `mod_rewrite` is available." As examples, I've done extensive development with Cgiapp that used Smarty and PEAR::DB; I've also used Savant and `Zend_Db`. I have users that report they love ADODB. I've used pretty URLs, but I've also often used GET or POST to determine the current run mode. The point is, Cgiapp merely provides an easy to use controller into which you can plug the model and view of your choice.

The other very important aspect of Cgiapp, to me, is application reusability. With Cgiapp2, this becomes even more of a feature, as applications can be customized via run-time plugins from the instance script. If an application has to be tied to a particular site structure, it's never completely reusable. But when it is configurable per instance, via templates, data store, and/or pre/post operation actions, it becomes distributable and pluggable. This means the possibility of applications like forums, galleries, contact forms, article systems, etc. that can be dropped in anywhere in a site and easily configured to match that site's look and feel. To me, this is invaluable.

With this explanation of Cgiapp under my belt, I plan to start blogging about uses of Cgiapp and Cgiapp2 to show how it can most optimally used. In the meantime, feel free to comment and ask questions!

**Update:** Changed language in third paragraph to put emphasis on Cgiapp as *not a framework* instead of anti-PEAR slant.
