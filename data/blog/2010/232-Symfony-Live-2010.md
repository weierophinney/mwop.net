---
id: 232-Symfony-Live-2010
author: matthew
title: 'Symfony Live 2010'
draft: false
public: true
created: '2010-02-17T11:39:43-05:00'
updated: '2010-02-21T18:53:00-05:00'
tags:
    - php
    - sflive2010
    - symfony
    - 'zend framework'
---
This week, I've been attending [Symfony Live](http://www.symfony-live.com/) in
Paris, speaking on integrating Zend Framework with Symfony. The experience has
been quite rewarding, and certainly eye-opening for many.

To be honest, I was a little worried about the conference — many see Symfony and
ZF as being in competition, and that there would be no cross-pollination. I'm
hoping that between [Fabien](http://fabien.potencier.org/),
[Stefan](http://www.leftontheweb.com/), and myself, we helped dispel that myth
this week.

<!--- EXTENDED -->

The fact of the matter is that no single project can be fully comprehensive, and
do everything perfectly. In my examinations of different frameworks, PHP and
otherwise, the places where they most differ and which generates the most
loyalty amongst users are the MVC approaches and tooling support. In good
frameworks, this is just a portion of the code, and the remainder is in support
libraries or plugins that extend that functionality.

This is true of both Symfony and Zend Framework. Symfony's development team has
chosen to focus on a very specific core of functionality related to the MVC
approach, which makes their maintenance job easier, and leads to a stable
product. Zend Framework's MVC implementation is offered as a group of separate
components, with components such as `Zend_Application` and `Zend_Tool` helping
to bring cohesion and structure to them.

What this means is that once you've developed the basic infrastructure of your
application, the scaffolding, you're now left with decisions about how to
implement the actual functionality of the application itself. The problem as I
see it is: how do you do that development? Many developers are myopic and will
not look beyond the framework they have chosen for for development. This can
lead to multiple implementations of the same code, and often leads to incomplete
implementations as well.

My feeling is that whenever you find yourself about to write new code, look to
see if somebody else has written the code already. Anybody — don't limit
yourself to your framework of choice. If I want to do serious HTML sniffing,
validation, and cleanup, I go to [HTMLPurifier](http://htmlpurifier.org/); if I
want a workflow component, I check out [eZ Components Workflow](http://www.ezcomponents.org/docs/api/latest/introduction_Workflow.html);
I always check [PEAR](http://pear.php.net/).

This week, I tried to spread this message within the
[Symfony](http://symfony-project.org) community, showing them how easy it is to
integrate ZF components within Symfony projects. The integration itself is
simple: instantiate the Zend autoloader, and start using ZF classes. This same
technique can be used to load PEAR, or eZComponents, or Doctrine 2, etc. The
trick is getting out of the "Not Invented Here" syndrome, letting go of your
ego, and using *other* people's code.

(Yes, I know we have code in ZF duplicating functionality in other libraries; in
most cases, we try and offer at least a new approach to the problem — but we
could do better.)

Fabien also made an interesting announcement. During a Q&A session with the
Symfony core team, he said that Symfony 2 will not write re-invent the wheel
when it doesn't need to — and announced that Symfony 2 will be using `Zend_Log`
and `Zend_Cache` instead of rewriting the current Symfony components. I find
this admirable — and it's something I'm hoping to do in a few places with Zend
Framework 2.0 as well, as I know there are features and code that others have,
quite simply, written better.

One last note in this ramble: With the various "2.0" versions of frameworks,
most projects are learning from both mistakes made as well as from the usage
patters of the developers adopting them. One of those lessons, to my mind, is
that no one framework can do it all well and by themselves. I fully expect to
see the next generation of frameworks making it trivial to pull features from
other frameworks and libraries in order to fill out functionality.
