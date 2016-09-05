---
id: 2016-08-17-zf-composer-autoloading
author: matthew
title: 'Using Composer to Autoload ZF Modules'
draft: false
public: true
created: '2016-08-17T12:15:00-05:00'
updated: '2016-08-17T12:15:00-05:00'
tags:
    - zendframework
    - php
    - programming
---

One aspect of [Zend Framework 3](https://framework.zend.com/blog/2016-06-28-zend-framework-3.html),
we paid particular focus on was leveraging the [Composer](https://getcomposer.org)
ecosystem. We now provide a number of Composer plugins for handling things such
as initial project installation, registering installed modules with the
application, and more. It's the "more" I particularly want to talk about.

<!--- EXTENDED -->

## Registering modules on install

With ZF2, we were able to realize the ability to install third-party modules
into existing applications, enabling a module ecosystem. The standard mantra for
install has been:

1. Install module: `composer require some/module`
2. Register module with application: edit `config/(application|modules).config.php`
   and add `Some\Module` to the list of modules.

This second item has been problematic:

- Easy to forget
- Easy to introduce a typo

For the v3 release, we wanted to solve this if we could. We were able to do so
via a Composer plugin, [zend-component-installer](https://docs.zendframework.com/zend-component-installer).

Module authors may add some metadata to their package now, like the following:

```json
"extra": {
  "zf": {
    "module": "Some\\Module"
  }
}
```

and, if the plugin is present in the user's application, on installation, it
will register the package as a module with the application! (Moreover, if you
later remove the package, it will remove it!)

We also added rules to allow specifying a package as a component; in this case,
the module is added to the *top* of the module list, to ensure that userland
modules can override its settings.

This ability to make a common task turn-key via Composer makes me happy.

## Autoloading your own modules via Composer

Recently, while working on Apigility, a collaborator made a suggestion: "We
recommend using Composer for autoloading, and yet Apigility creates modules that
use the default module autoloading capabilities; couldn't we create a utility
for enabling Composer autoloading of a generated module?"

This turned out to be really easy to accomplish, and we ended up creating a new
package, [zfcampus/zf-composer-autoloading](https://apigility.org/documentation/modules/zf-composer-autoloading),
to make it re-usable.

Let's say you've created a new module in your ZF or Apigility application, named
`Blog`. Chances are, you put a `Module.php` file at the module's root, and it
either contains a `Blog\Module` class, or requires a classfile from your source
tree that will. Let's setup autoloading:

```bash
$ composer require --dev zfcampus/zf-composer-autoloading
$ ./vendor/bin/autoload-module-via-composer Blog
```

Done.

The package ships with only a vendor binary, and that does the following:

- Adds an entry in your `composer.json` to autoload the module.
- Regenerates the Composer autoloading rules.

It will autodetect the module type (PSR-0 or PSR-4) based on the detected
directory structure, but also allows you to specify the type via a CLI flag. You
can also tell it where your Composer binary is, if it's not on your path.

Once you're done, if you defined a `getAutoloaderConfig()` method in your
module, you can now remove it, as it's redundant!

This tool will work with existing ZF2 and Apigility installs, of any version.

Composer all the things!
