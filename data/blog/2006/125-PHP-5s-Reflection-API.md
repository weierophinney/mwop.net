---
id: 125-PHP-5s-Reflection-API
author: matthew
title: 'PHP 5''s Reflection API'
draft: false
public: true
created: '2006-10-09T10:58:00-04:00'
updated: '2006-10-09T14:44:47-04:00'
tags:
    - php
---
As [Davey Shafik noted recently](http://pixelated-dreams.com/archives/251-More-Web-Services.html), he and I have been working together on some web services for the [Zend Framework](http://framework.zend.com/). In doing so, I've become very familiar with PHP 5's Reflection API, and am coming to love it.

When I first read about the Reflection API in a pre-PHP 5 changelog, my initial reaction was, "who cares?" I simply failed to see how it was a useful addition to the language. Having done some projects recently that needed to know something about the classes they are using, I now understand when and how it can be used. It shines when you need to work with classes that may not be defined when you write your code — any code that dispatches to other classes, basically.

<!--- EXTENDED -->

So, what sorts of things can you do with the Reflection API? Here's a list of some of the things I've done:

- Determine if a method exists in a class
- Retrieve and grab metainformation from a class, method, or function's phpdoc docblock (I used this heavily in developing `Zend_Server_Reflection`)
- Determine if a method is static, public, private, or protected
- Retrieve function/method parameters and determine position, whether or not the parameter is optional and what the default value might be, and the name of the parameter (i.e., what variable name is used to identify it)
- Invoke a function or method, with a variable number of arguments. This can be used in place of `call_user_func`/`call_user_func_array()`, and `$method->invoke()` allows for static method calls as well (I had to file a [bug with php.net](http://bugs.php.net/bug.php?id=38992) as static calls aren't allowed for `invokeArgs()`).
- **Instantiate an object instance with a variable number of arguments to the constructor** using `newInstanceArgs()`. This is something I've looked for for some time now; previously, the only solutions were to use `eval()` (yuck!) or have your constructors all accept an associative array of arguments. This is a much nicer, more flexible, solution.

The various `Reflection` classes can all be extended. However, since they're all very interrelated, I've found it easier to proxy them, and override methods as necessary. For instance, in the `Zend_Server_Reflection` tree, I needed class reflection to return an array of `Zend_Server_Reflection_Methods`, which did quite a few pieces of introspection on the docblock (getting method prototypes, hinting to parameters the variable types and descriptions, etc.). So, I defined something like this:

```php
class Reflection_Class
{
    public function __construct(ReflectionClass $r)
    {
        $this->_reflection = $r;

        foreach ($r->getMethods() as $method) {
            $this->_methods[] = new Reflection_Method($method);
        }
    }

    public function __call($method, $args)
    {
        if (method_exists($r, $method)) {
            return $r->{$method}($args);
        }
    }

    public function getMethods()
    {
        return $this->_methods;
    }
}
```

Obviously, this is shorthand, but you get the idea.

The Reflection API came in very handy with the various server components as we could have a central set of classes for doing function and class introspection that could then be used to define the dispatch callbacks the server could utilize. Additionally, I implemented a `__wakeup()` method that basically restores the entire reflection architecture, allowing us to serialize the server definitions between calls — which greatly reduces the amount of processing that needs to occur on subsequent calls.

We're also using it in the MVC components, specifically in the Dispatchers. Again, this allows us to (a) determine if a method exists for dispatch, and (b) call it with any arguments we may need. It also allows us to easily instantiate action controller objects using variable numbers of arguments.

If you're doing any sort of coding for a plugin architecture, I highly recommend getting to know the Reflection API; it's very powerful and can add some very nice, simple, flexibility to your code.
