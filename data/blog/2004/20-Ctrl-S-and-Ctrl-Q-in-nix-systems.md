---
id: 20-Ctrl-S-and-Ctrl-Q-in-nix-systems
author: matthew
title: 'Ctrl-S and Ctrl-Q in *nix systems'
draft: false
public: true
created: '2004-01-25T14:08:42-05:00'
updated: '2004-09-20T13:30:33-04:00'
tags:
    - linux
    - personal
---
I just ran into this not long ago, and wish I'd discovered it years ago. Basically, `Ctrl-S` *suspends* a process, while `Ctrl-Q` *resumes* it. This is useful when in `g/vim` or `screen` and you manage to lock up your application because you accidently hit `Ctrl-S` when reaching for another key combo.