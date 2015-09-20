---
id: 261-Code-Generation-with-ZendCodeGenerator
author: matthew
title: 'Code Generation with Zend\CodeGenerator'
draft: false
public: true
created: '2011-03-25T11:08:09-04:00'
updated: '2011-03-25T22:02:34-04:00'
tags:
    - php
    - 'zend framework'
---
Zend Framework has offerred a code generation component since version 1.8, when
we started shipping `Zend_Tool`. `Zend_CodeGenerator` largely mimics PHP's
Reflection API, but does the opposite: it instead generates code.

Why might you want to generate code?

- You can use it as an assistive form of "copy and paste" for common tasks (as
  an example, it's used in `zf.sh` to generate controller classes and action
  methods).
- You might want to generate code from configuration, to remove the "compile"
  phase of generating objects from configuration values. This is often done to
  improve performance in situations that rely heavily on configurable values.

`Zend\CodeGenerator` in the ZF2 repository is largely ported from Zend Framework
1, but also includes some functionality surrounding namespace usage and imports.
I used it this week when working on some prototypes, and found it useful enough
that I want to share some of what I've learned.

<!--- EXTENDED -->

## Basics

In most cases, you'll need to look through the API methods to get an idea of
what you can create. The various classes are all in the `Zend\CodeGenerator\Php`
namespace (the subnamespace is so that we might include code generation for
formats and languages other than PHP at some future point), and they include:

- `Docblock\Tag\LicenseTag` (generate "license" annotations for docblocks)
- `Docblock\Tag\ParamTag` (generate "param" annotations for docblocks)
- `Docblock\Tag\ReturnTag` (generate "return" annotations for docblocks)
- `PhpBody` (generate arbitrary PHP content; typically to fill files or method calls)
- `PhpClass` (generate PHP classes)
- `PhpDocblock` (generate PHP docblocks)
- `PhpDocblockTag` (generate arbitrary dockblock annotations)
- `PhpFile` (generate PHP files)
- `PhpMethod` (generate PHP class methods)
- `PhpParameterDefaultValue` (generate default parameter values for PHP method/function arguments)
- `PhpParameter` (generate PHP method/function parameters)
- `PhpProperty` (generate PHP class properties)
- `PhpPropertyValue` (generate PHP property value arguments; i.e., the default property value on instantiation)
- `PhpValue` (generate arbitrary PHP value assignment statements)

In most cases, you can call the `setContent()` and/or `setName()` methods; other methods will be available based on context. All classes also contain a `generate()` method which will generate code based on the current state of the object.

Most of these classes aren't of much use in isolation, but instead interact with other objects in order to create the expected code.

As an example, the prototype I was building was generating a PHP class file. The requirements included:

- Setting the namespace
- Defining one or more class imports
- Defining a class, which extended another class
- Defining several methods for that class, with code; in at least one case, the method generated also expected arguments

This was actually relatively easy; the hardest part was generating the actual code body for the individual methods!

As an example, we'll generate a class skeleton now:

```php
use Zend\CodeGenerator\Php as CodeGen;
$file = new CodeGen\PhpFile();
$file->setNamespace('Application')
     ->setUses('Zend\Di\DependencyInjectionContainer', 'DIC');
     
$class = new CodeGen\PhpClass();
$class->setName('Context')
      ->setExtendedClass('DIC');

$get = new CodeGen\PhpMethod();
$get->setName('get')
    ->setParameters(array(
        new CodeGen\PhpParameter(array('name' => 'name')),
        new CodeGen\PhpParameter(array(
            'name' => 'params',
            'defaultValue' => new CodeGen\PhpParameterDefaultValue(array(
                'value' => array(),
            )),
        )),
    ));

$class->setMethod($get);

$file->setClass($class);

echo $file->generate();
```

The above will generate the following:

```
<?php

namespace Application;

use Zend\Di\DependencyInjectionContainer as DIC;

class Context extends DIC
{

    public function get($name, $params = array())
    {
    }


}
```

Some tips and gotchas:

- As in most of ZF, any setter method can be configured. Key names correspond to
  the setter method, minus "set", and with the first letter lowercased — so,
  `setName()` can be triggered by passing a configuration key of "name";
  `setDefaultValue()` with "defaultValue".
- You don't *need* to provide objects in most cases; you can pass arrays
  representing the configuration values for the object type expected. As an
  example, passing an array of values as an item to `setParameter()` will pass
  the configuration to the constructor of `PhpParameter`. That said, I found it
  was more predictable and easier to read to do the explicit object
  declarations.
- If your default parameter value is an array, you have to jump through some
  hoops. Normally, you could simply specify the value you want to use to the
  `setDefaultValue()` method (or "defaultValue" key), but arrays are treated as
  configuration. As such, you will need to create a `PhpParameterDefaultValue`
  explicitly in these cases (as I did in the above example).
- In the above, I didn't generate anything more than a skeleton. However, in my
  actual prototype, I was generating code for the body content of methods. I
  found that `sprintf` was my friend here, as was a variable or constant
  representing the amount of indentation. As an example:

  ```php
  $caseStatements = array();
  foreach ($definitions as $definition) {
      // ...
      
      $caseStatement  = '';
      foreach ($cases as $case) {
          $caseStatement .= sprintf("%scase '%s':\n", $indent, $case);
      }
      $caseStatement .= sprintf("%sreturn \$this->%s();\n", str_repeat($indent, 2), $getter);
      $caseStatements[] = $caseStatement;
  }

  $switch = sprintf("switch (\$name) {\n%s}\n", implode($caseStatements, "\n"));

  $method->setBody($switch); // PhpMethod object
  ```

  Which in turn generated the following:

  ```php
  switch ($name) {
      case 'foo':
      case 'My\Component\Foo':
          $this->getMyComponentFoo();

  }
  ```

Why?
----

It may look like a lot of code, and you may be wondering, "why bother?" The
point, though, is that it's predictable and testable — which gives it a nudge
over a templated solution. I can basically ensure the structure I want similar
to constructing XML using `DOM` — and alter it later if I want to.

Additionally, in my particular use case — and, really, it's a common use case —
I'm using a predictable configuration structure, and want to generate something
over and over again. As my configuration changes, I want to be able to update
the code, without needing to worry if I forgot something or introduced a new
typo (other than those I created in my configuration file). The point is really
that this is code I'll be writing again and again, so having a tool to generate
it will save me time.

In addition, in this particular use case, the generated code is faster than
running the code that generates it, as it prevents a "configuration" step in the
final production phase. By generating code, I can circumvent things such as
Reflection, use more efficient practices (e.g., usage of `call_user_func()` or
direct method calls instead of `call_user_func_array()`), and introduce type
hinting in an area that relied on strings previously.

Fini
----

There's a ton of functionality available in `Zend\CodeGenerator`, and I only
scratched the tip of the iceberg in this post. What use cases do *you* have for
code generation? what tips to *you* have to share?
