---
id: 44-PHP-Continue-processing-after-script-aborts
author: matthew
title: 'PHP: Continue processing after script aborts'
draft: false
public: true
created: '2004-10-08T08:50:23-04:00'
updated: '2004-10-08T08:50:28-04:00'
tags:
    - personal
    - php
---
Occasionally, I've needed to process a lot of information from a script, but I
don't want to worry about PHP timing out or the user aborting the script (by
clicking on another link or closing the window). Initially, I investigated
[register_shutdown_function()](http://php.net/register_shutdown_function) for
this; it will fire off a process once the page finishes loading. Unfortunately,
the process is still a part of the current connection, so it can be aborted in
the same way as any other script (i.e., by hitting stop, closing the browser,
going to a new link, etc.).

However, there's another setting initialized via a function that can override
this behaviour — i.e., let the script continue running after the abort. This is
[ignore_user_abort()](http://php.net/ignore_user_abort). By setting this to
true, your script will continue running after the fact.

This sort of thing would be especially good for bulk uploads where the upload
needs to be processed — say, for instance, a group of images or email addresses.
