mwop.net
========

This is the code behind mwop.net.

It is built on:

- [Mezzio](https://docs.mezzio.dev/) - The entire site consists of different middleware and handlers for accomplishing different tasks.
  These include a contact form, a blog, static pages, and webhooks.
- [league/plates](http://platesphp.com) for templating.
- [mezzio/mezzio-session-cache](https://docs.mezzio.dev/mezzio-session-cache/) for managing sessions.
- [laminas-paginator](https://docs.laminas.dev/laminas-paginator/) and [laminas-feed](https://docs.laminas.dev/laminas-feed/) for implementing several features of the blog.
- [laminas-cli](https://docs.laminas.dev/laminas-cli/) for implementing console commands for the site.
- [phly-redis-task-queue](https://github.com/phly/phly-redis-task-queue) for deferring tasks and running recurring jobs.

If you see bugs in the website, please feel free to provide a pull request!
