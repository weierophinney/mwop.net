---
id: 2023-12-21-advent-tmux
author: matthew
title: 'Advent 2023: tmux'
draft: false
public: true
created: '2023-12-21T18:52:00-06:00'
updated: '2023-12-21T18:52:00-06:00'
tags:
    - advent2023
    - tmux
---
I use terminal-based programs a lot.

It should be obvious to anyone following my blog that I use editors in the vim family.
But there are a slew of other tools I use from the CLI: docker, phpunit, phpcs, psalm, pandoc, ssh, ngrok, and more.
Often, I'll be editing a file, and need to run another program, and reference what I'm editing: running unit tests, linters, or static analysis often fall in this category.

Sure, I could use a tabbed terminal, but then I can't have the results of running the program right next to the editor.
So for this, I use a terminal multiplexer; specifically, I use [tmux](https://github.com/tmux/tmux/wiki).

<!--- EXTENDED -->

### Terminal multiplexer?

A _terminal multiplexer_ allows multiple pseudo-terminal sessions within a single display.
It's kind of like a window manager for a terminal.
At its most basic, it offers multiple _windows_ that you can switch between.
However, most terminal multiplexers also allow you to split a window into multiple _panes_, each with its own session.
A good terminal multiplexer allows you to arrange the panes side by side, stacked vertically, or a combination of the two.
Additionally, many allow you to run one or more _sessions_, which you can detach from and re-attach to; you can even have multiple people attach at the same time (which is a fun way to screenshare!).

The terminal multiplexer I was first introduced to was [screen](https://www.gnu.org/software/screen/).
It's been around for longer than I've been using Linux, and mostly gets the job done.
However, another project has largely supplanted it: tmux.
It offers all the same features, and, by default, behaves essentially the same way as screen. 
However, it has a number of improvements, including the ability to _extend_ its feature set, which makes it a compelling replacement.

### Extensions?

Yes, extensions.

I use several, but the following are the ones that have been most useful:

- tmux-continuum and tmux-resurrect give me continuous and on-demand saving of my session, as well as restoration of sessions. 
  With these in place, I can be reasonably safe from things like my machine crashing or power outages, as whatever I was doing will be present when I start the session again.
  (Within reason; things like docker sessions will of course need to be restarted.)
- tmux-open allows me to navigate my cursor to a filename, and open it with the system default program, or `$EDITOR`.
- tmux-yank copies text I highlight using tmux's visual highlighting to the system clipboard.
  It's rare I _don't_ want this!
- tmux-tilish provies auto-tiling of panes.
  This means that spawning a new pane will position it in a predictable location.
- tmux-navigator allows me to switch between panes using vim navigation bindings; coupled with the vim/nvim plugin vim-tmux-navigator, I can jump between vim/nvim panes and tmux panes as if they were the same thing.

### Where to learn more?

I've used tmux for over a decade.
In 2016, Brian Hogan published a fantastic book via Pragmatic Bookshelf, [tmux 2: Productive Mouse-Free Developmet](https://pragprog.com/titles/bhtmux2/tmux-2/).
I can't recommend it highly enough; most of my configuration was cribbed from the examples he provided, and it's written in a way that gently reveals features as you need them.
