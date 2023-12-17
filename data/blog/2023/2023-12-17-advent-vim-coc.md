---
id: 2023-12-17-advent-vim-coc
author: matthew
title: 'Advent 2023: (n)vim Plugins: coc.nvim'
draft: false
public: true
created: '2023-12-17T13:32:00-06:00'
updated: '2023-12-17T13:32:00-06:00'
tags:
    - advent2023
    - neovim
    - nvim
    - vim
---
I've used vim and variants since 2001.
In 2019, a friend introduced me to [coc.nvim](https://github.com/neoclide/coc.nvim), which turned out to be my initial gateway to nvim, which I adopted a year or two later.

<!--- EXTENDED -->

### What is coc.nvim?

The plugin name is an acronym for "Conquer of Completion", and its goal is to "Make your Vim/Neovim as smart as VS Code".
While it can be used with either vim or neovim, it has some optimizations under the hood to allow usage with neovim's [language server protocol support](https://neovim.io/doc/user/lsp.html), which allows it to expose more features and perform better when using that editor.

The language server protocol exposes features like (per the neovim documentation) "go-to-definition, find-references, hover, completion, rename, format, refactor, etc., using semantic whole-project analysis."

In practice, this means the following:

- You get signature hints.
  In PHP, this will pick up hints from the signature, but also hints from docblocks.
- You get completion.
  Hitting `<Tab>`, you'll see a list of possible matches.
  - When in PHP, if the match is for a class name, it will add the import statements for you.
  - Also in PHP, if the match is for a method that exists in an implemented interface or a parent class, it will complete the signature for you, and perform any imports required.
- If your cursor is on something like a property name, a method name, a class name, or more, you can jump to the definition (I have this mapped to `gd`, for "**g**o to **d**efinition).
- Depending on the language server in use, you may also get the ability to do limited refactoring, such as changing a variable/property name throughout a file, or renaming a method and all calls to it.

Interestingly enough, the LSP is what is used under-the-hood by IDEs like VS Code, so I'm getting the same features I'd get using a dedicated IDE!

These features helped me so much, I ponied up for an [Intelephense](https://intelephense.com/) license.

### If neovim already supports language servers natively, why use coc.nvim?

I use a variety of languages, and the configuration for each varies widely.
On top of that, when I started using neovim, I wasn't yet familiar with Lua, which is how you configure things like the LSP.
But even once I learned... the documentation for the various LSP implementations is often missing or inscrutable; it's hard to know what options are available, and how to modify the behavior programmatically.

coc.nvim just takes care of it, gives me a single location for configuration, gives me a single set of keybindings to remember, and provides a unified interface for operations like jumping to definitions, performing refactors, and more.

Less futzing, more working.

### Why was this helpful?

Prior to adopting coc.nvim, I used things like [ctags](https://ctags.sourceforge.net/) to provide some limited completion and ability to jump to definitions.
However, this was problematic in that I would forget to regenerate ctags for a project when I added or changed dependencies, as well as when adding my own code; they were always out-of-date.
Using them, I ended up having to keep a mental map in my head of the project, so that I could open the class file with a definition when needing to understand what the signature allowed, or what the method returned.
This approach was fine when I was doing development every day, or working on familiar code bases, but often neither of these are true anymore.

### Why not just use a "real" IDE?

Listen, IDEs are great.
But even when I've taken time to get to know an IDE, I've found I'm just not as productive when using one.
Vim and its descendants are highly optimized for touch typists, and make movement and selection ridiculously fast compared to using a mouse, trackpad, or visual pointer.

Having tools like coc.nvim allow me to get some of the chief benefits of an IDE from within my preferred editor.
Sure, I don't get advanced refactoring tools, but I can always reach for [Rector](https://getrector.com/) when I need to.
Debugging? [vim-debug](https://github.com/vim-vdebug/vdebug) gives me most of what I would need, and I can reach for a visual IDE if I need more granular control.

The LSP and coc.nvim give me the best of both worlds: excellent tooling for a touch typist, with IDE features that give contextual information when I need it.
