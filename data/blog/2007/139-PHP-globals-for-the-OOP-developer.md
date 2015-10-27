---
id: 139-PHP-globals-for-the-OOP-developer
author: matthew
title: 'PHP globals for the OOP developer'
draft: false
public: true
created: '2007-05-19T09:42:48-04:00'
updated: '2007-05-20T12:58:14-04:00'
tags:
    - php
---
**Update:** I evidently simplified the issue too much, and have had several
people rightly comment on the bogosity of the issue. However, there are still
situations where `$GLOBALS` does not act as expected, and I outline these in
[my next entry](/blog/140-globals-continued.html).

* * * * *

In my [previous entry](/matthew/archives/138-Start-Writing-Embeddable-Applications.html),
I ranted about the use of globals in popular PHP applications, and how they
make embedding said applications difficult. I develop using object-oriented
practices, and can honestly say I can't recall ever having slung a global
variable around in my own code. Globals seem hackish to me, and as a result,
trying to get applications that use them to behave correctly has been a
challenge.

One of the applications I had in mind was [Serendipity](http://www.s9y.org),
the software that powers this blog. I was attempting to create a Zend Framework
action controller that wraps my s9y instance so that I can do things such as
apply ACLs from my website to selected entries, as well as pull the sitewide
skeleton out from s9y so that I only have to maintain one version of it (I had
one version for s9y, and another for my own content featured on the site
(resume, contact form, etc.).

I tried importing the various config files into my action method prior to
invoking the actual s9y bootstrap, but no dice. I also tried modifying the s9y
config files to use the notation `$GLOBALS['serendipity']` around the
serendipity configuration variables (s9y uses a single multi-dimensional array
for all configuration options). This didn't work, either; s9y functions that
called global `$serendipity` were still getting a null value.

So, I did a little closer reading in the manual [section on predefined
variables](http://php.net/language.variables.predefined), I discovered
something interesting in the description of `$GLOBALS` (emphasis mine):

> Contains a reference to every variable which is *currently* available within
> the global scope of the script.

Interestingly, the section on variable scope didn't make this distinction at
all. Basically, if the variable you reference via `$GLOBALS` does not already
exist, assigning it *does nothing*. It doesn't even raise a notice. It just
silently goes ahead, leaving you thinking you set a new global variable, but in
fact, you cannot assign new globals via `$GLOBALS`; you can only modify
*existing* variables in the global scope.

So, I got around the issue by putting this in my front controller bootstrap:

```php
$serendipity = null;
```

After that, I was able to create a wrapper action controller for s9y very easily:

```
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
        $this->_helper->viewRenderer->initView(null, null, array('noRender' => true));
    }

    public function indexAction()
    {
        global $serendipity;
        chdir($_SERVER['DOCUMENT_ROOT'] . '/path/to/s9y');
        include './index.php';
        chdir($_SERVER['DOCUMENT_ROOT']);
    }
}
```

Note that I don't do any output buffering; this is because the ZF dispatcher
takes care of that for me. All I need to do is execute the s9y bootstrap.

So, the lesson to learn from all this: if you need to wrap an application that
uses globals, find out what all of them are, and declare them in the global
namespace — just setting them to null is enough — in your application
bootstrap.
