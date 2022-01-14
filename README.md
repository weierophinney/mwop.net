mwop.net
========

This is the code behind mwop.net.

It is built on:

- [Mezzio](https://docs.mezzio.dev/) - The entire site consists of different middleware and handlers for accomplishing different tasks.
  These include a contact form, a blog, static pages, and webhooks.
- [OpenSwoole](https://www.swoole.co.uk) and the [Mezzio Swoole bindings](https://docs.mezzio.dev/mezzio-swoole/), for serving the site, as well as providing async task workers.
- [league/plates](http://platesphp.com) for templating.
- [mezzio/mezzio-session-cache](https://docs.mezzio.dev/mezzio-session-cache/) for managing sessions.
- [laminas-inputfilter](https://docs.laminas.dev/laminas-inputfilter/), [mezzio-csrf](https://docs.mezzio.dev/mezzio-csrf/), and [SendGrid](https://sendgrid.com/) for handling contact forms.
- [laminas-paginator](https://docs.laminas.dev/laminas-paginator/) and [laminas-feed](https://docs.laminas.dev/laminas-feed/) for implementing several features of the blog.
- [laminas-cli](https://docs.laminas.dev/laminas-cli/) for implementing console commands for the site.

If you see bugs in the website, please feel free to provide a pull request!
