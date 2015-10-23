---
id: 38-Whats-keeping-that-device-in-use
author: matthew
title: 'What''s keeping that device in use?'
draft: false
public: true
created: '2004-09-11T21:27:11-04:00'
updated: '2004-09-20T13:59:20-04:00'
tags:
    - linux
    - personal
---
Ever wonder what's keeping that device in use so you can't unmount it? It's
happened to me a few times. The tool to discover this information? `lsof`.

Basically, you type something like: `lsof /mnt/cdrom` and it gives you a `ps`-style
output detailing the PID and process of the processes that are using the cdrom.
You can then go and manually stop those programs, or kill them yourself.
