---
id: 220-Autoloading-Doctrine-and-Doctrine-entities-from-Zend-Framework
author: matthew
title: 'Autoloading Doctrine and Doctrine entities from Zend Framework'
draft: false
public: true
created: '2009-08-20T15:17:46-04:00'
updated: '2009-08-25T17:07:57-04:00'
tags:
    0: php
    2: 'zend framework'
---
A number of people on the mailing list and twitter recently have asked how to
autoload Doctrine using Zend Framework's autoloader, as well as how to autoload
Doctrine models you've created. Having done a few projects using Doctrine
recently, I can actually give an answer.

The short answer: just attach it to `Zend_Loader_Autoloader`.

Now for the details.

<!--- EXTENDED -->

First, make sure the path to the `Doctrine.php` file is on your `include_path`.

Next, `Zend_Loader_Autoloader` allows you to specify "namespaces" (not actual
PHP namespaces, more like class prefixes) it can autoload, both for classes it
will autoload, as well as for autoload callbacks you attach to it. Typically,
you include the trailing underscore when doing so:

```php
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Foo_');
$autoloader->pushAutoloader($callback, 'Bar_');
```

However, because Doctrine has a master class for handling common operations,
"Doctrine", we have to omit the trailing underscore so that the `Doctrine` class
itself may be autoloaded. We need to do two different operations: first, add a
namespace to `Zend_Loader_Autoloader` for Doctrine (which will allow us to
autoload the Doctrine class itself, as well as the various doctrine subcomponent
classes), and then register the Doctrine autoloader (which will be used by
Doctrine to load items such as table classes, listeners, etc.):

```php
$autoloader->registerNamespace('Doctrine')
           ->pushAutoloader(array('Doctrine', 'autoload'), 'Doctrine');
```

This takes care of the Doctrine autoloader; now, let's turn to Doctrine models.

First, tell Doctrine that you want to autoload. You do this by telling it to use
"conservative" model loading (shorthand for lazyloading or autoloading), and to
autoload table classes:

```php
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(  
    Doctrine::ATTR_MODEL_LOADING, 
    Doctrine::MODEL_LOADING_CONSERVATIVE
);
$manager->setAttribute(  
    Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, 
    true
);
```

From here, you need to ensure you actually *can* autoload the models. Normally,
you tell Doctrine where to find models, but we're in a Zend Framework
application, so let's leverage ZF conventions.

I typically put my model code with my application code:

```
application
|-- Bootstrap.php
|-- configs
|-- controllers
|-- models                 <- HERE
|-- modules
|   `-- blog
|       |-- Bootstrap.php
|       |-- controllers
|       |-- forms
|       |-- models         <- HERE
|       |-- services
|       `-- views
`-- views
```

Zend Framework already provides mechanisms for autoloading application resources
via `Zend_Loader_Autoloader_Resource` and `Zend_Application_Module_Autoloader`.
Assuming you've extended `Zend_Application_Module_Bootstrap` in your module
bootstraps, you're basically already set. The trick has to do with your table
classes; your table classes *must* be placed in the same directory as your
models, and they *must* be named exactly the same as your models, with the
suffix "Table".

For example, if you had the class `Blog_Model_Entry` extending `Doctrine_Record`
in the file `application/modules/blog/models/Entry.php`, the related table class
would be `Blog_Model_EntryTable` in the file
`application/modules/blog/models/EntryTable.php`.

I automate most of this setup via my `Bootstrap` class, which typically looks as
follows:

```php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAppAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'App',
            'basePath'  => dirname(__FILE__),
        ));
        return $autoloader;
    }

    protected function _initDoctrine()
    {
        $this->getApplication()->getAutoloader()
                               ->pushAutoloader(array('Doctrine', 'autoload'));

        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(
            Doctrine::ATTR_MODEL_LOADING, 
            Doctrine::MODEL_LOADING_CONSERVATIVE
        );
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

        $dsn = $this->getOption('dsn');
        $conn = Doctrine_Manager::connection($dsn, 'doctrine');
        $conn->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        return $conn;
    }
}
```

Within your configuration, you need to add two keys: one for registering the
Doctrine namespace with the default autoloader, and another for the dsn:

```php
autoloaderNamespaces[] = "Doctrine"
dsn = "DSN to use with Doctrine goes here"
```

I also have a script that I use to load all model classes at once in order to do
things like generate the schema or test interactions. I'll blog about those at a
later date. Hopefully the above information will help one or two of you out
there trying to integrate these two codebases!

#### Updates

- **2009-08-21:** added information about registering Doctrine namespace with default autoloader
