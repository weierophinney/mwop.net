---
id: 2023-12-08-advent-wayland-path
author: matthew
title: 'Advent 2023: $PATH on Wayland'
draft: false
public: true
created: '2023-12-08T08:27:00-06:00'
updated: '2023-12-08T08:27:00-06:00'
tags:
    - advent2023
    - wayland
---
This year, I finally switched over to using [Wayland](https://wayland.freedesktop.org/) on my desktop.
I figured that with Ubuntu planning to use it by default in 24.04 and Fedora already defaulting to it, it was likely stable enough to use.

I've had a few issues in the past when I've tried it, primarily around screen sharing, but thankfully most if not all issues I've hit in the past are solved.
I did run into one issue, though: when setting startup programs or using Alt-F2 to run a program, it wasn't finding stuff on my path.

<!--- EXTENDED -->

### How did I solve it?

Unlike XWindows, Wayland doesn't use your configured shell when starting up, unless that shell is `/bin/sh` and/or `/bin/bash`.
Since I use [zsh](https://zsh.sourceforge.io), this meant that my configured path... wasn't, as far as Wayland was concerned.
It only ever looks at `$HOME/.profile`, which is incredibly minimal.

I have a number of additional locations on my `$PATH`, but the one where the majority of any custom programs are installed is `$HOME/.local/bin`.

The solution was simple: add any paths I need Wayland to be able to see in `$HOME/.profile`:

```bash
if [ -d "$HOME/.local/bin" ]; then
    PATH="$HOME/.local/bin:$PATH"
fi
```
