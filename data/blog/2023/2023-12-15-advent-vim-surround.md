---
id: 2023-12-15-advent-vim-surround
author: matthew
title: 'Advent 2023: (n)vim Plugins: vim-surround'
draft: false
public: true
created: '2023-12-15T17:43:00-06:00'
updated: '2023-12-15T17:43:00-06:00'
tags:
    - advent2023
    - neovim
    - nvim
    - vim
---
I've [blogged about vim a number of times](https://mwop.net/blog/tag/vim).
I've been using vim or its descendents for 22 years now; I switched to [neovim](https://neovim.io) a few years back, but it's compatible with the existing vim ecosystem.
(The primary differences, to my mind, are that it has a more optimized engine which is more performant, and that you can now configure and extend it using [Lua](https://www.lua.org) if you want.
Otherwise... it's just vim.)

I used to "collect" plugins, but at this point, particularly since switching over to neovim, I've reduced my plugins quite a bit, to only those I use on a regular basis.

So, I figured today, I'd start a mini-series as part of my Advent 2023 blogging, on some of my most used plugins.

Today's plugin: [vim-surround](https://github.com/tpope/vim-surround).

<!--- EXTENDED -->

### What does it do?

vim-surround, at it's heart, allows you to surround a selection with a character or pair of characters.

Why is this useful?

Let's say you're writing an exception message, and suddenly realize that you forgot to put it in quotes as you close the parens:

```php
throw new RuntimeException(This is the exception message I will present)
```

Normally, I might use `f(` to jump to the first paren, hit `a` to append at that point, and then hit `'` to add the initial quote, and Esc to return to normal mode.
I'd repeat, but use `t)` to jump to the character before the closing paren.

With vim-surround, I can leverage vim's selection strokes to quickly do this.

- Hit `vi(` from anywhere within the parentheses, which selects everything between then.
  I can even do this when on the ")" character; it performs the same selection.
- Hit Shift-s to trigger vim-surround
- Type `'` to surround the selected text with the quotes.

This may not seem like much, but the amount of effort it saves is tremendous:

- I use it a ton when writing Markdown, to mark words or phrases as italic (`_`), bold `**` (this one requires doing the selection twice), or just to add quotes.
- In HTML, I can use it to add tags!
  When you trigger vim-surround, you can start typing a tag — e.g. `<em>` — and vim-surround will wait until the tag is complete, and then surround the selection with that tag.
- In code, I'll often use it to surround a selection with parens or square brackets, or add quotes (as demonstrated above).

### Sidebar: efficient selection

Vim provides actions that give you the ability to select (`v`), replace (`c`), delete (`d`), and yank (`y`) text.
These actions operate on the selection you provide.

The selection `w` indicates to operate on the current _word_, and by default, it operates from the current position to the next _word boundary_.
This is important: the selection is really a _boundary_ you wish to select _to_.

There are modifiers you can provide for the selection: "i" means "inner", and will select everything _up to_ the boundary, but not the boundary itself, but, more importantly, the boundary _to either side of the current position_.t

There are some boundaries that are particularly interesting: "(" and ")", "{" and "}", "[" and "]", "&lt;"/"&gt;", and both quote styles, when paired with "i" or "a" modifiers, will select _bewteen pairs_.

In the example above, I used `vi(` to select everything _inside_ the parens.
"a" means "all", and acts like "i", but selects everything up to and including the boundary.
If I'd used `vaw` in the example, it would have selected everything inside the parens, _as well as_ the parens.

You can be really efficient in your text selection and manipulation knowing these rules, and it's when using these rules that vim-surround shines.

### Final Thoughts

It's the simplicity of operations such as text selection and using vim-surround that are a key reason for sticking with vim all these years.
They allow me to efficiently edit text without needing to leave the home row of the keyboard, or requriing a mouse.

So far in my career, I've avoided RSI, and I credit tools like this as a big part of that.
