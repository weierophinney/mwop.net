---
id: 2023-12-18-advent-vim-fugitive
author: matthew
title: 'Advent 2023: (n)vim Plugins: vim-fugitive'
draft: false
public: true
created: '2023-12-18T15:34:00-06:00'
updated: '2023-12-18T15:34:00-06:00'
tags:
    - advent2023
    - git
    - neovim
    - nvim
    - vim
---
Because I've spent most of my professional life coding, I've also spent a lot of time using source control.
I've been using specifically [git](https://git-scm.com/) for many years (even pre-dating the Zend Framework migration from [Subversion](https://subversion.apache.org)).
While I typically use a terminal multiplexer (for me, that's [tmux](https://github.com/tmux/tmux/wiki); for others, that might be [screen](https://www.gnu.org/software/screen/)), and can move to another pane or create one quickly in order to run source control commands, doing so interrupts flow.

That's where [vim-fugitive](https://github.com/tpope/vim-fugitive) comes into play.

<!--- EXTENDED -->

### What does it solve?

Fugitive integrates with git, plain and simple.
It exposes a number of commands and functions that allow you to do common operations quickly, but also has some deeper bindings to allow doing more complex things such as viewing a file from previous commits, or performing a diff between the staged and working version, or using `git blame` within vim.

### How do I use it?

Admittedly, I use a very small subset of what Fugitive provides.

On a daily basis, I use `:Gwrite` to stage changes, and `:G` to view the status of the working tree.
When in the status view, I often use `cc` to **c**ommit **c**hanges, which splits open a pane for writing the commit message.
I also use `:GRemove` when I want to remove a file from the tree. 

Something else that has come in handy when reviewing code with others: `:GBrowse` can open the file in the canonical repository, using the visual selection as the line range, allowing you to quickly share a link to specific code to review.

### Final Thoughts

This plugin does exactly what it says on the tin.
I love the fact that it integrates with the underlying `git` command, as that follows the Unix Philosophy of doing one thing well, and piping out to other processes to perform complex behavior.
For me, the fact that I can stay directly within my editor and still get full access to git when needed is tremondously powerful.
