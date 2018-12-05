---
id: 2018-12-05-on-the-fly-exceptions
author: matthew
title: 'Creating Exception types on-the-fly in modern PHP'
draft: false
public: true
created: '2018-12-05T16:26:00-06:00'
updated: '2018-12-05T16:26:00-06:00'
tags:
    - php
    - programming
    - oop
---

We pioneered a pattern for exception handling for Zend Framework back as we
initially began development on version 2 around seven years ago. The pattern
looks like this:

- We would create a marker `ExceptionInterface` for each package.
- We would extend [SPL exceptions](http://php.net/manual/en/spl.exceptions.php)
  and implement the package marker interface when doing so.

What this gave users was the ability to catch in three ways:

- They could catch the most specific exception type by class name.
- They could catch all package-level exceptions using the marker interface.
- The could catch general exceptions using the associated SPL type.

<!--- EXTENDED -->

So, as an example:

```php
try {
    $do->something();
} catch (MostSpecificException $e) {
} catch (PackageLevelExceptionInterface $e) {
} catch (\RuntimeException $e) {
}
```

This kind of granularity is really nice to work with. So nice that some
standards produced by [PHP-FIG](https://www.php-fig.org/) now ship them, such as
[PSR-11](https://www.php-fig.org/psr/psr-11/), which ships a
`ContainerExceptionInterface` and a `NotFoundExceptionInterface`.

> One thing we've started doing recently as we make packages support only PHP 7
> versions is to have the marker `ExceptionInterface` extend the `Throwable`
> interface; this ensures that implementations **must** be able to be thrown!

So, what happens when you're writing a one-off implementation of something that
is expected to throw an exception matching one of these interfaces?

Why, use an anonymous class, of course!

As an example, I was writing up some documentation that illustrated a custom
`ContainerInterface` implementation today, and realized I needed to throw an
exception at one point, specifically a `Psr\Container\NotFoundExceptionInterface`. 
I wrote up the following snippet:

```php
$message = sprintf(/* ... */);
throw new class($message) extends RuntimeException implements
    NotFoundExceptionInterface {
};
```

Done!

This works because `RuntimeException` takes a message as the first
constructor argument; by extending that class, I gain that behavior. Since
`NotFoundExceptionInterface` is a marker interface, I did not need to add any
additional behavior, so this inline example works out-of-the-box.

What else are _you_ using anonymous classes for?
