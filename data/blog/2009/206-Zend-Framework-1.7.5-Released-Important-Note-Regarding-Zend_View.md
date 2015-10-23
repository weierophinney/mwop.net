---
id: 206-Zend-Framework-1.7.5-Released-Important-Note-Regarding-Zend_View
author: matthew
title: 'Zend Framework 1.7.5 Released - Important Note Regarding Zend_View'
draft: false
public: true
created: '2009-02-17T11:33:18-05:00'
updated: '2009-02-23T07:44:10-05:00'
tags: {  }
---
Yesterday, we released [Zend Framework 1.7.5](http://framework.zend.com/download/latest). It contains a somewhat
controversial security fix to `Zend_View` that could potentially affect some use
cases of the component; I'm providing details on that security fix as well as
how to work around it here.

<!--- EXTENDED -->

A user filed an issue report showing a potential Local File Inclusion vulnerability in `Zend_View`'s `setScriptPath()` method: if user input were used to specify the script path, then it was possible to trigger the LFI. The vulnerability was completely contrived; no sane developer should ever configure the view script paths using user input. However, it pointed out another very real LFI attack vector.

The attack vector is, once again, a situation of trusting unfiltered input, but this time it's a much more likely scenario. In this particular case, let's say we had `Zend_View` configured as follows:

```php
$view->addScriptPath('/var/www/application/views/scripts');
```

We then accepted the following input, and passed it to the `render()` method: "../../../../etc/passwd".

The LFI vector was then triggered, as `render()` actually allowed for parent directory traversal.

ZF 1.7.5 now has a check for such notation (`../` or `..\\`), and throws an exception when detected.

On #zftalk.dev, several contributors noted that this could potentially break some of their applications. In their situations, they were using parent directory traversal, but not from user input. In such a situation, since they have control over the value, they felt the check was better left to userland code.

To accomodate this, we introduced a flag, "lfiProtectionOn". By default, this flag is true, enabling the check. You may turn it off in one of two ways:

```php
// At instantiation:
$view = new Zend_View(array(
    'lfiProtectionOn' => false,
));

// Programmatically, at any time:
$view->setLfiProtection(false);
```

Including this security fix was a hard decision. On the one hand, we try very hard to keep backwards compatibility between versions. On the other, there's also a very real responsibility to our users to keep them secure. Hopefully, the addition of the LFI protection flag above will help ease the migration issues.

For more information on this change, you can [read the relevant manual page](http://framework.zend.com/manual/en/zend.view.migration.html).
