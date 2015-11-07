---
id: 212-The-simplest-Zend_Form-decorator
author: matthew
title: 'The simplest Zend_Form decorator'
draft: false
public: true
created: '2009-04-03T08:30:00-04:00'
updated: '2009-04-07T10:20:30-04:00'
tags:
    0: php
    2: 'zend framework'
---
I've been seeing ranting and general confusion about [Zend_Form](http://framework.zend.com/manual/en/zend.form.html) decorators (as well as the occasional praises), and thought I'd do a mini-series of blog posts showing how they work.

<!--- EXTENDED -->

First, some background on the [Decorator design pattern](http://en.wikipedia.org/wiki/Decorator_pattern). One common technique is to define a common interface that both your originating object and decorator will implement; your decorator than accepts the originating object as a dependency, and will either proxy to it or override its methods. Let's put that into code to make it more easily understood:

```php
interface Window
{
    public function isOpen();
    public function open();
    public function close();
}

class StandardWindow implements Window
{
    protected $_open = false;

    public function isOpen()
    {
        return $this->_open;
    }

    public function open()
    {
        if (!$this->_open) {
            $this->_open = true;
        }
    }

    public function close()
    {
        if ($this->_open) {
            $this->_open = false;
        }
    }
}

class LockedWindow implements Window
{
    protected $_window;

    public function __construct(Window $window)
    {
        $this->_window = $window;
        $this->_window->close();
    }

    public function isOpen()
    {
        return false;
    }

    public function open()
    {
        throw new Exception('Cannot open locked windows');
    }

    public function close()
    {
        $this->_window->close();
    }
}
```

You then create an object of type `StandardWindow`, pass it to the constructor of `LockedWindow`, and your window instance now has different behavior. The beauty is that you don't have to implement any sort of "locking" functionality on your standard window class — the decorator takes care of that for you. In the meantime, you can pass your locked window around as if it were just another window.

One particular place where the decorator pattern is useful is for creating textual representations of objects. As an example, you might have a "Person" object that, by itself, has no textual representation. By using the Decorator pattern, you can create an object that will act as if it were a Person, but also provide the ability to render that Person textually.

In this particular example, we're going to use [duck typing](http://en.wikipedia.org/wiki/Duck_typing) instead of an explicit interface. This allows our implementation to be a bit more flexible, while still allowing the decorator object to act exactly as if it were a Person object.

```php
class Person
{
    public function setFirstName($name) {}
    public function getFirstName() {}
    public function setLastName($name) {}
    public function getLastName() {}
    public function setTitle($title) {}
    public function getTitle() {}
}

class TextPerson
{
    protected $_person;

    public function __construct(Person $person)
    {
        $this->_person = $person;
    }

    public function __call($method, $args)
    {
        if (!method_exists($this->_person, $method)) {
            throw new Exception('Invalid method called on TextPerson: ' .  $method);
        }
        return call_user_func_array(array($this->_person, $method), $args);
    }

    public function __toString()
    {
        return $this->_person->getTitle() . ' '
               . $this->_person->getFirstName() . ' '
               . $this->_person->getLastName();
    }
}
```

In this example, you pass your Person instance to the TextPerson constructor. By using method overloading, you are able to continue to call all the methods of Person — to set the first name, last name, or title — but you also now gain a string representation via the `__toString()` method.

This latter example is getting close to how `Zend_Form` decorators work. The key difference is that instead of a decorator wrapping the element, the element has one or more decorators attached to it that it then injects itself into in order to render. The decorator then can access the element's methods and properties in order to create a representation of the element — or a subset of it.

`Zend_Form` decorators all implement a common interface, `Zend_Form_Decorator_Interface`. That interface provides the ability to set decorator-specific options, register and retrieve the element, and render. A base decorator, `Zend_Form_Decorator_Abstract`, provides most of the functionality you will ever need, with the exception of the rendering logic.

Let's consider a situation where we simply want to render an element as a standard form text input with a label. We won't worry about error handling or whether or not the element should be wrapped within other tags for now — just the basics. Such a decorator might look like this:

```php
class My_Decorator_SimpleInput extends Zend_Form_Decorator_Abstract
{
    protected $_format = '<label for="%s">%s</label><input id="%s" name="%s" type="text" value="%s"/>';

    public function render($content)
    {
        $element = $this->getElement();
        $name    = htmlentities($element->getFullyQualifiedName());
        $label   = htmlentities($element->getLabel());
        $id      = htmlentities($element->getId());
        $value   = htmlentities($element->getValue());

        $markup  = sprintf($this->_format, $id, $label, $id, $name, $value);
        return $markup;
    }
}
```

Let's create an element that uses this decorator:

```php
$decorator = new My_Decorator_SimpleInput();
$element   = new Zend_Form_Element('foo', array(
    'label'      => 'Foo',
    'belongsTo'  => 'bar',
    'value'      => 'test',
    'decorators' => array($decorator),
));
```

Rendering this element results in the following markup:

```html
<label for="bar-foo">Foo</label><input id="bar-foo" name="bar[foo]" type="text" value="test"/>
```

You could also put this class in your library somewhere, inform your element of that path, and refer to the decorator as simply "SimpleInput" as well:

```php
$element = new Zend_Form_Element('foo', array(
    'label'      => 'Foo',
    'belongsTo'  => 'bar',
    'value'      => 'test',
    'prefixPath' => array('decorator' => array(
        'My_Decorator' => 'path/to/decorators/',
    )),
    'decorators' => array('SimpleInput'),
));
```

This gives you the benefit of re-use in other projects, and also opens the door for providing alternate implementations of that decorator later (a topic for another post).

Hopefully, the above overview of the decorator pattern and this simple example will shed some light on how you can begin writing decorators. I'll be writing additional posts in the coming weeks showing how to leverage decorators to build more complex markup, and will update this post to link to them as they are written.

**Update:** Fixed text in thrown exception to reflect actual class name; updated label generation to use id for "for" attribute, per comment from David.

#### Also in this series:

- [From the inside out: How to layer decorators](/blog/213-From-the-inside-out-How-to-layer-decorators.html)
