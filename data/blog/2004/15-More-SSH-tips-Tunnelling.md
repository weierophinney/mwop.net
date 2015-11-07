---
id: 15-More-SSH-tips-Tunnelling
author: matthew
title: 'More SSH tips: Tunnelling'
draft: false
public: true
created: '2004-01-23T17:19:14-05:00'
updated: '2004-09-20T13:20:30-04:00'
tags:
    - linux
    - personal
---
I wrote up a short tutorial today on the IT wiki about SSH tunneling. What I
didn't know is that you can start a tunnel *after* you've already ssh'd to
another machine. Basically, you:

- Press Enter
- Type `~C`

and you're at an `ssh>` prompt. From there, you can issue the tunnel command of
your choice: `-R7111:localhost:22`, for instance.
