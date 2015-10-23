---
id: 10-SSH-tips-and-tricks
author: matthew
title: 'SSH tips and tricks'
draft: false
public: true
created: '2004-01-21T20:08:44-05:00'
updated: '2004-09-13T23:08:29-04:00'
tags:
    - personal
---
In trying to implement some of the hacks in *Linux Server Hacks*, I had to go to the ssh manpage, where I discovered a number of cool tricks.

1. In order to get key-based authentication (i.e., passwordless) working, the `$HOME/.ssh` directory must be mode `0700`, and all files in it must be mode `0600`. Once that's setup properly, key-based authentication works perfectly.
2. You can have a file called *config* in your `$HOME/.ssh` directory that specifies user-specific settings for using SSH, as well as a number of *host*-specific settings:
  -   `Compression yes` turns on compression
  -   `ForwardX11 yes` turns on X11 forwarding by default
  -   `ForwardAgent yes` turns on ssh-agent forwarding by default
  -   *Host*-based settings go from one *Host* keyword to the next, so place them at the end of the file. Do it in the following order:

    ```apacheconf
    Host nickname
    HostName actual.host.name
    User username_on_that_host
    Port PortToUse
    ```

    This means, for instance, that I can ssh back and forth between home using the same key-based authentication and the same ssh-to script ([more below](#ssh-to)) I use for work servers -- because I don't have to specify the port or the username.

I mentioned a script called `ssh-to` earlier. This is a neat little hack from the server hacks book as well. Basically, you have the following script in your path somewhere:

```bash
#!/bin/bash
ssh -C `basename $0` $*
```

Then, elsewhere in your path, you do a bunch of `ln -s /path/to/ssh-to /path/to/$HOSTNAME`, where `$HOSTNAME` is the name of a host to which you ssh regularly; this is where specifying a host nickname in your `$HOME/.ssh/config` file can come in handy. Then, to ssh to any such server, you simply type `$HOSTNAME` at the command line, and you're there!
