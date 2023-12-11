---
id: 2023-12-11-advent-makefile
author: matthew
title: 'Advent 2023: Makefile'
draft: false
public: true
created: '2023-12-11T12:23:00-06:00'
updated: '2023-12-11T12:23:00-06:00'
tags:
    - advent2023
    - bash
    - make
    - makefile
---
I like to automate common workflows when I can, particularly for web projects.
As an example, I may have different Docker Compose setups for development versus production, and having to remember to add the `-f {compose file name}` argument can be tedious and error prone.

Being a long-time Linux user, I've used `make` a lot, and am fairly comfortable with `Makefile`, so I often turn to it for these tasks.

<!--- EXTENDED -->

### Why make?

First, it's lean.
While `make` is often not on a system by default, it's generally provided via a single package with no additional dependencies.
In other words, installing it is not going to bring a whole gcc suite or other language runtime onto your system.

Second, it makes it possible to build a workflow out of independent targets.
As an example, I can build discrete targets for "cs" (coding standards), "sa" (static analysis), and "test" (unit tests), but then create a workflow that calls each of these:

```makefile
qa: cs test sa
```

This granularity is declarative, and encourages thinking about individual processes.

Third, I can compose multiple `Makefile`s in a project.
For instance, if I want to have processes for different contexts in the project (e.g., assets, data migrations, containers), I can have a `Makefile` in each directory, and in the application root, `include` the other ones as needed within a given target.

Fourth, I can call other targets from within a target.
Let's say I need to conditionally build something; I can perform the condition, and, if it passes, call `$(MAKE) {some other target}` to accomplish it.

Fifth, and this one really goes back to the first point, I don't need to have any other runtimes or languages installed for it to work.
It's all just shell, with some additional semantics.

Finally, most shells have autocompletion enabled for `Makefile` targets and variables, which means you get great autocompletion without needing to do any extra work.

### How do I use it?

`Makefile` syntax has a few, let us say, _oddities_, which can make it a challenge to work with.
Below are a few of the things I've learned along the way.

First, A former colleague shared a really useful snippet for providing usage with me:

```makefile
##@ Help

help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[0-9a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-40s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
```

This snippet is _awesome_.

Essentially, it looks for two different patterns:

- `##@ {text}`: this is used to split different _sections_ for reporting usage.
  You can see an example in the snippet above: `##@ Help` starts a section named "Help", and it is denoted in bold cyan.

- `{target}: ... ## {help text}`: If you have a comment starting with two hashtags following a `Makefile` target, it will list that target, and use the `{help text}` you provide as the usage help for it.
  You can also see this in the snippet, for the `help:` target.

Next, it's often useful to be able to resolve paths relative to the directory in which the `Makefile` resides.
The following variable declaration does that:

```makefile
HERE := $(dir $(realpath $(firstword $(MAKEFILE_LIST))))
```

Within your `Makefile`, you can then use that variable to resolve a path:

```makefile
prod-build: ## Build production containers
    docker compose -f "$(HERE)/docker-compose.prod.yml" build
```

One thing I get caught up on all the time: the shell used by default in a `Makefile` is `/bin/sh`.
To change it, you need to define the `SHELL` variable:

```makefile
SHELL=/bin/bash
```

Finally, I find colors a useful way to provide context for messages.
ASCII supports some ANSI escape sequences for providing color, as well as making text bold or underlined.
As such, I create functions like the following:

```makefile
MK_RED = echo -e "\e[31m"$(1)"\e[0m"
```

Which you then call as follows:

```makefile
@$(call MK_RED,"This is an error message")
```

Finally, an important note: `Makefile` uses tabs for indentation.
If you forget, and use spaces, it will error on you.
Use an editor or IDE that understands `Makefile` syntax when you write them, to avoid issues!

### Making it simpler

Because all of the above is a lot to remember, I created a template:

```makefile
#!make
############################## Variables ##############################
HERE := $(dir $(realpath $(firstword $(MAKEFILE_LIST))))
SHELL=/bin/bash
#######################################################################

############################### Colors ################################
# Call these using the construct @$(call {VAR},"text to display")
MK_RED = echo -e "\e[31m"$(1)"\e[0m"
MK_GREEN = echo -e "\e[32m"$(1)"\e[0m"
MK_YELLOW = echo -e "\e[33m"$(1)"\e[0m"
MK_BLUE = echo -e "\e[34m"$(1)"\e[0m"
MK_MAGENTA = echo -e "\e[35m"$(1)"\e[0m"
MK_CYAN = echo -e "\e[36m"$(1)"\e[0m"
MK_BOLD = echo -e "\e[1m"$(1)"\e[0m"
MK_UNDERLINE = echo -e "\e[4m"$(1)"\e[0m"
MK_RED_BOLD = echo -e "\e[1;31m"$(1)"\e[0m"
MK_GREEN_BOLD = echo -e "\e[1;32m"$(1)"\e[0m"
MK_YELLOW_BOLD = echo -e "\e[1;33m"$(1)"\e[0m"
MK_BLUE_BOLD = echo -e "\e[1;34m"$(1)"\e[0m"
MK_MAGENTA_BOLD = echo -e "\e[1;35m"$(1)"\e[0m"
MK_CYAN_BOLD = echo -e "\e[1;36m"$(1)"\e[0m"
MK_RED_UNDERLINE = echo -e "\e[4;31m"$(1)"\e[0m"
MK_GREEN_UNDERLINE = echo -e "\e[4;32m"$(1)"\e[0m"
MK_YELLOW_UNDERLINE = echo -e "\e[4;33m"$(1)"\e[0m"
MK_BLUE_UNDERLINE = echo -e "\e[4;34m"$(1)"\e[0m"
MK_MAGENTA_UNDERLINE = echo -e "\e[4;35m"$(1)"\e[0m"
MK_CYAN_UNDERLINE = echo -e "\e[4;36m"$(1)"\e[0m"

# Semantic names
MK_ERROR = $(call MK_RED,$1)
MK_ERROR_BOLD = $(call MK_RED_BOLD,$1)
MK_ERROR_UNDERLINE = $(call MK_RED_UNDERLINE,$1)
MK_INFO = $(call MK_BLUE,$1)
MK_INFO_BOLD = $(call MK_BLUE_BOLD,$1)
MK_INFO_UNDERLINE = $(call MK_BLUE_UNDERLINE,$1)
MK_SUCCESS = $(call MK_GREEN,$1)
MK_SUCCESS_BOLD = $(call MK_GREEN_BOLD,$1)
MK_SUCCESS_UNDERLINE = $(call MK_GREEN_UNDERLINE,$1)
######################################################################

.PHONY: help

default: help

##@ Help

help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[0-9a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-40s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

# Create sections using \#\#@ {section name}; see above "Help" comment
# Provide help for a target using comments starting with two hashtags; see above "help" target
# For a great tutorial of Makefile features, see https://makefiletutorial.com/
```

I put this in `$HOME/.config/makefile-init/Makefile`, and then created the following script in `$HOME/.local/bin/makefile-init`:

```bash
#!/bin/bash

target=.

if [[ -n $1 ]]; then
    target=$1

    if [[ ! -d "${target}" ]]; then
        echo -e "\e[31mThe path '${target}' is not a valid directory\3[0m"
        exit 1
    fi
fi

cp "${HOME}/.config/makefile-init/Makefile" "${target}"

echo -e "\e[32mCreated ${target}/Makefile\e[0m"
```

When I need to add a `Makefile` to a project, I then just type `makefile-init`, and get the template.

Note the link at the end of the template; [Makefile Tutorial](https://makefiletutorial.com/) brilliantly outlines the features and behaviors of a `Makefile` in a way that the official man and info pages never have!

By starting with this template:

- Typing `make` with no arguments or targets displays the usage.
- You can provide usage instructions via comments.
- You're using bash by default.
- You get a number of ways to colorize text out of the box.
- You can reference files relative to the `Makefile`.
- You have a link to a reference in case you are unsure how to write something in the `Makefile`.

### Final thoughts

I use a number of programming languages, and it's fascinating to see how each eventually re-creates make and `Makefile` functionality.
[Composer](https://getcomposer.org) and [npm](https://www.npmjs.com) each did it via their "scripts" configuration, with Composer even allowing you to define an array of scripts as a target, recreating how `Makefile` has always done it.
While these are perfectly serviceable, it's often nice to have something that does _not_ require the language runtime or an additional language-specific binary on the system; particularly if some tasks are not dependent on it.
`make` and `Makefile` satisfy that wonderfully.
