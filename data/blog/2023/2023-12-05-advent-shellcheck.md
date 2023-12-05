---
id: 2023-12-05-advent-shellcheck
author: matthew
title: 'Advent 2023: Shellcheck'
draft: false
public: true
created: '2023-12-05T08:17:00-06:00'
updated: '2023-12-05T08:17:00-06:00'
tags:
    - advent2023
    - bash
    - shellcheck
---

As you may have noted from previous posts in this [Advent 2023 series](https://mwop.net/blog/tag/advent2023), I find myself using Bash more and more often.
This has certainly been a surprise for a career PHP developer, but it is what it is.

With PHP, there are a wealth of QA tools, from unit testing, to enforcing coding standards, to static analysis.
What about with Bash?
Well, the tools exist, and there's one I literally cannot write Bash without: [Shellcheck](https://www.shellcheck.net).

<!--- EXTENDED -->

### What is Shellcheck?

**Shellcheck is static analysis for shell scripts.**

It alerts you to problematic constructs and potential errors in your scripts, and provides you with information on the problem, linking to a description of the issue.

### How I use Shellcheck

While Shellcheck is designed as a CLI tool, I don't typically run it as a separate process.
Instead, I have shellcheck enabled as a language server in neovim (well, technically, within my [coc.nvim](https://github.com/neoclide/coc.nvim) configuration).
This means that I get realtime information when writing shell scripts about potential errors in my scripts.

As an example, consider the following screenshot:

![Screenshot demonstrating a Shellcheck tooltip](/images/blog/2023-12-05-shellcheck.png)

It suggests that I should not use `ls | grep` combinations to prevent issues if filenames have non-alphanumeric characters in them.
It then references something called "SC2010".
This is where it gets really great: you can do an internet search for "shellcheck sc2010", which will take you to the [Shellcheck wiki entry for issue 2010](https://www.shellcheck.net/wiki/SC2010); if you know the shellcheck wiki address, you can just append the issue ID to that URL and go directly to it!.

The Shellcheck wiki holds a wealth of detail, and will discuss not just that something _is_ a problem, but _why_, and _how to write better code_.
In the above example, because filesystem names do not necessarily need to be alphanumerics, they can be problematic values to pipe to `grep`, and the suggestion is to instead use globs or a `for` loop over glob entries.

### Final thoughts

Shellcheck has allowed me to write better, more robust Bash scripts by notifying me when I am using potentially problematic features of the shell.
It's also clued me in to a number of shell features I didn't know existed (as an example, the construct `mapfile -t {variable_name} < <({command})` to create an array of values from the output of a command).
I find I'm a far more confident Bash developer today because of this tool.
