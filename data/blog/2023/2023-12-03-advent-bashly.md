---
id: 2023-12-03-advent-bashly
author: matthew
title: 'Advent 2023: Bashly'
draft: false
public: true
created: '2023-12-03T10:10:00-06:00'
updated: '2023-12-03T10:10:00-06:00'
tags:
    - advent2023
    - bash
    - bashly
---

For the third day of my [2023 advent blogging](https://mwop.net/blog/tag/advent2023), I'm covering a tool I've really leaned hard on the last few years: [Bashly](https://bashly.dannyb.co/).

<!--- EXTENDED -->

### What did I need to solve?

I am a huge fan of [symfony/console](https://symfony.com/doc/current/components/console.html), and in the [Laminas Project](https://getlaminas.org), we even built [laminas/cli](https://docs.laminas.dev/laminas-cli/) on top of it.
It's a hugely versatile project that allows building robust command line applications in PHP.

However, there's a number of cases where PHP is not the right choice.
A couple years back for work, we realized we should have a script to make provisioning [ZendPHP](https://www.zend.com/products/zendphp-enterprise) predictable and work across Linux distributions.
We needed to enable setting up the OS-specific package repository; installing PHP; installing common SAPIs; installing, enabling, and disabling extensions; and more.
Because PHP would not even be on the system, and because we'd be largely delegating to system commands, symfony/console was the exact wrong answer here.
On top of that, it made sense that this command should also be present in our containers, as well as the marketplace cloud images we provide.

As such, we needed to be able to run the command with either no or very few dependencies.
The common denominator here, then, is a shell script.

At first, I considered going the route of a minimal shell, usually mapped to `/bin/sh` on systems, but that varies a ton.
RHEL and CentOS actually use bash.
Debian-based distros typically use dash.
Alpine uses busybox.

While all of these are POSIX shells, the feature-set differs quite a bit, and targeting the minimum features would actually make the job harder in many cases (particularly in places where pattern matching or array structures are needed).

So, I made the decision that we'd target bash.
But the next issue was more user-focused: how could we (a) provide good user help instructions, (b) provide argument and flag matching, and (c) make clear what _type_ of information was being provided as feedback (e.g., via color)?
These are all features present in symfony/console, and when you look at really good command line tools such as apt, git, and others, they provide a wealth of features like this to help guide users.

Eventually, I found [Bashly](https://bashly.dannyb.co/).

> #### Tangent: bash vs python
>
> I recently read an article advocating for using Python instead of bash if you'll be doing any pattern matching, conditional logic, functions, or loops.
> (I cannot link it, unfortunately, as I didn't bookmark it!)
> The rationale is that basically all Linux systems have some version of Python available, and it's better suited for these operations.
>
> While I understand the argument, I found myself disagreeing when I saw the hoops a Python dev has to go through, and in the end, you generally have far more than a single script that you have to distribute.
> Additionally, if you do not know what Python version is supported on the system, or need to develop a script that will work across multiple systems and Python versions, you have a lot more work cut out.
>
> All that said, I'll likely investigate Python more for these sorts of tasks in the future.

### What is Bashly?

To quote the Bashly landing page:

> Bashly is a command line application (written in Ruby) that lets you generate feature-rich bash command line tools.

To get started, you use a YAML file (eww, yaml) to define the commands you want to accept, their arguments, any flags/options, and optionally validations for each.
You then run Bashly to generate the commands, which are just bash scripts; you edit those to provide the actual functionality.

Once you've finished writing the command functionality, you run Bashly again, and it generates a single bash script with all the functionality, which you can then distribute.

### What makes Bashly compelling?

I've now used Bashly to write easily a dozen CLI apps, from smaller personal tools to things that build a matrix of Docker images, as well as run tests on them.
There's a lot I like about it.

- **You don't need to install Bashly locally to use it.**
  Bashly itself is written in Ruby, which means local installation would require a Ruby interpreter in the correct version range, and likely a package manager, and likely some additional tools.
  Conveniently, you can run it via a Docker image, and I alias `bashly` to `docker run --rm -it --user $(id -u):$(id -g) --volume "$PWD:/app" dannyben/bashly`.
  You can call it as if it were a local command, and after the first run when the image is pulled, it's basically instantaneous.

- The command scripts that Bashly generates are inlined into functions within the final script, which means **you can scope all variables, avoiding global state**.
  As a developer, this is something I can appreciate, as I know that anything I define will not be overwritten by things like ENV variables.

- You can **define a source library** within your Bashly application.
  Every script in that library should define exactly one function.
  This approach allows you to write utility functions that you call again and again within your commands.
  This modular approach lends itself well to testing and re-use.
  Paired with tools like [Shellcheck](https://www.shellcheck.net), it has allowed me to write more maintainable bash.

- It supports **nested commands**.
  Think of this like when you run `git` commands such as `stash`: you can `push`, `pop`, `apply`, and more.
  You can create nesting like this with Bashly as well, and each level gets its own usage and help text.

- You can keep **command configuration as YAML frontmatter** in the command script.
  This may not seem like a big deal, but the fact that your Bashly configuration file can just import the commands it uses, and the command keeps its own configuration — arguments, flags, validations, help text, etc. — makes it far easier to understand at a glance what the command _expects_ and may have available.
  I like keeping related things together.

- Did I mention **help text**?
  You define help/usage text with each command, so every command has a `--help|-h` flag available to provide that usage.

- There's **color support**!
  You can use color to provide context for whatever you print out to the user.

- There's **completion support**.
  If you enable it, your users can then use the functionality provided to add completion when using the application, and this is hugely useful, particularly if you have a lot of commands, subcommands, arguments, and flags.

- There's support for **user settings**.
  If enabled, your users can supply an INI file with configuration settings, which your script can then grab and use.

And there's even more features, which I won't go into, but many of which I've used.

### Final Thoughts

I know a lot of folks hate bash, and if you're one of them, you're not likely to be convinced to use Bashly.
However, if you need to write a re-usable tool that will be interacting with the operating system, and which will be compatible with a broad swath of Linux systems, bash is a good choice, and Bashly provides a wealth of features for making maintainable, usable command line applications.

I really cannot recommend it highly enough!
