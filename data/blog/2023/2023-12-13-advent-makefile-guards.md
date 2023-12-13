---
id: 2023-12-13-advent-makefile-guards
author: matthew
title: 'Advent 2023: Makefile: guard targets'
draft: false
public: true
created: '2023-12-13T17:10:00-06:00'
updated: '2023-12-13T17:10:00-06:00'
tags:
    - advent2023
    - bash
    - make
    - makefile
---
A couple days ago, I [wrote about Makefile](/blog/2023-12-11-advent-makefile.html).
Today, I'm going to show a quick tip for writing "guard" targets.

<!--- EXTENDED -->

### Guard targets?

Maybe you have a target that requires root to run.
Or maybe you have one that requires that a `.env` file is present.
You _could_ check for these conditions within your target:

```makefile
deploy:
	@$(call MK_INFO,"Checking for root")
	@if [[ "$(shell id -u)" != 0 ]]; then $(call MK_ERROR,"This target requires root"); exit 1; fi
    @$(call MK_INFO,"Checking for .env")
	@if [[ ! -f .env ]]; then $(call MK_ERROR,".env file is missing"); exit 1; fi
```

What if you need to do these checks in multiple targets?
You can't really make functions of these, so how can you prevent duplication?

The thing is, `make` already supports these sorts of things, because any target can already specify its _prerequisites_, which are just _other targets_!

When I define a prerequisite target that only exists to ensure certain conditions are met as a prerequisite to other targets, I call it a **guard target**.

Let's refactor:

```makefile
root:
	@$(call MK_INFO,"Checking for root")
	@if [[ "$(shell id -u)" != 0 ]]; then $(call MK_ERROR,"This target requires root"); exit 1; fi

deploy: root .env ## Deploy the app
	# do the actual work...
```

Let's dissect this:

- The `root` target checks to see if the current user is root.
  If not, it spits out an error message (see my [previous article on Makefile to understand that MK_ERROR usage](/blog/2023-12-11-advent-makefile.html) works), and then exits with an error status.
- The `deploy` target marks `root` and `.env` as prerequisites; if either fails, it won't run.
- Note that the `root` target does not have a `##` comment; this means it won't show up in my usage messages (though I can still call it separately if I want).

Wait, what about `.env`?

The default assumption of `make` is that _targets_ are _files_.
If the file exists, the target will not be executed, as the _file already exists_, so no work needs to be done.
By specifying `.env` as a target, we're saying that `deploy` can only run if `.env` exists!

### But if targets are files...

So, if targets are files, how does `make` work at all for things like deployment?

Again, the assumption is if the file _does not exist_, then `make` has work to do, and if that file is listed as a target, then it needs to execute that target.

Generally, the targets you create for things like web application deployment will not have corresponding files or directories, so `make` will happily see that the target exists in your `Makefile`, no filesystem entry exists, and execute it.
The fact that it doesn't actually _generate_ the target file is of no matter.

### What if the target _does_ exist?

What if the target name **does** have a corresponding file in the filesystem?

Mark it as a "PHONY" target:

```makefile
.PHONY: .env
```

If you do this, then even if the `.env` file exists, the `.env` target, if it exists, will still be run.

### Final Thoughts

Guard targets are a great way to add preconditions to build targets.
They are re-usable and succinct, and can be used to provide useful error messages to guide usage.
Better, they leverage the native aspects of a `Makefile` and `make`, and with good naming conventions, it becomes easy to identify _what_ the prerequisite is for a given target without even needing to see how it's defined.
