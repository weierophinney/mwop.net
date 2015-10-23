---
id: 128-PHP-Best-Practices
author: matthew
title: 'PHP Best Practices'
draft: false
public: true
created: '2006-11-01T00:01:05-05:00'
updated: '2006-11-01T08:31:01-05:00'
tags:
    - php
---
Yesterday, [Mike](http://mikenaberezny.com/) and I presented our session "Best Practices of PHP Development" at this year's Zend Conference. It was a marathon three hour tutorial first thing in the morning, and we had an incredible turnout, with some fairly enthusiastic people in the audience.

[Download the slides slides for PHP Best Practices](/uploads/php_development_best_practices.pdf "php_development_best_practices.pdf").

<!--- EXTENDED -->

We ended up cutting a ton from the session the night before, as we discovered we actually had way too much material. Amongst the cuts were sections on:

- Comparisons of different coding standards. I'd worked up a comparison of eZ Components and Zend Framework standards to contrast against PEAR's.
- Functional testing. Mike put a lot of effort into the unit testing section, and I'd done an additional section on functional testing — testing against fixtures, such as test databases, sandbox services, etc.
- Repository layout. Mike actually talked about this briefly, but we'd intended to show some designs for subversion layouts, and how to create and use branches and tags.
- Subversion hook scripts. We mentioned their existence, and some uses, but we'd hoped to show how to add these to your repository, and some sample scripts.
- Mailman. How to setup archived mailing lists.
- Capistrano. Mike mentioned this tool in the talk, but did not have time to go into examples of usage.

Basically, most of the topics we covered could have easily been a session in their own right. However, having a big block of time to cover the spectrum I believe helps show how to integrate the individual solutions into a set of cohesive development practices.

I hope to blog about some of the areas we had to skip in coming months.

To those attendees who came to the session yesterday, thank you for being a great audience!
