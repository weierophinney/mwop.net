mwop.net
========

This is the code behind mwop.net.

It is built on:

- [Expressive](https://github.com/mezzio/mezzio) - The entire site consists of
  different middleware and handlers for accomplishing different tasks. These
  include a contact form, social authentication, a blog, and static pages.
- [Swoole](https://www.swoole.co.uk) and the [Expressive Swoole
  bindings](https://docs.zendframework.com/mezzio-swoole/), for serving
  the site, as well as providing async task workers.
- [league/plates](http://platesphp.com) for templating.
- [mezzio/mezzio-session-cache](https://docs.zendframework.com/mezzio-session-cache/) for managing sessions.
- [league/oauth2-client](http://oauth2-client.thephpleague.com/) for social authentication.
- [zend-inputfilter](https://docs.zendframework.com/zend-input-filter/),
  [zend-expressive-csrf](https://docs.zendframework.com/zend-expressive-csrf/),
  and [SwiftMailer](https://swiftmailer.symfony.com/) for handling
  contact forms.
- [zend-paginator](https://docs.zendframework.com/zend-paginator/)
  and [zend-feed](https://docs.zendframework.com/zend-feed/) for implementing
  several features of the blog.
- [symfony/console](https://symfony.com/doc/current/components/console.html) for
  implementing console commands for the site.

If you see bugs in the website, please feel free to provide a pull request!
