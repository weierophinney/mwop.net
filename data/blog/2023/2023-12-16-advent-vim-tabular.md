---
id: 2023-12-16-advent-vim-tabular
author: matthew
title: 'Advent 2023: (n)vim Plugins: tabular'
draft: false
public: true
created: '2023-12-16T14:56:00-06:00'
updated: '2023-12-16T14:56:00-06:00'
tags:
    - advent2023
    - neovim
    - nvim
    - vim
---
[Yesterday, I discussed vim-surround](/blog/2023-12-15-advent-vim-surround.html).
Today I'm going to discuss another plugin I've used a ton: [tabular](https://github.com/godlygeek/tabular).

<!--- EXTENDED -->

### What problem does it solve?

Let's say you have a PHP associative array declaration:

```php
[
    "name" => "Matthew",
    "status" => "online",
    "url" => "https://mwop.net"
]
```

Now, for some folks, this declaration is fine.
However, I tend to prefer aligning the declarations, as it helps me visually parse the block more easily.

Now, I could go to each line, and insert space before the `=>` assignment operators.
This works, and for a short declaration like in this example, it's relatively quick.
But when the declaration is larger, or the sizes of the keys vary a lot, this can become tedious quickly.

Another place I've used it is Markdown tables:

```markdown
| Name | Status | URL |
| ---- | ------ | --- |
| Matthew | online | mwop.net |
| An Example | offline | example.com |
| API | online | api.example.com |
```

This demonstrates the problem even better, as you can see that the columns do not align, which makes understanding if each row has all the required columns harder.

### Tabular

The tabular plugin allows you to easily _tabularize_ text.
You visually select the rows you want to line up, and then invoke it, providing a _search_ string (a regular expression) for matching the character(s) to align against.
It then does the work of determining how much space to add to each column to get things to line up.

Going back to the original example, I'd select all the entries within the array, and then:

- `:` to enter command mode
- `Tabularize /=>` to invoke tabular, and tell it to align against `=>`
- `Enter`

and then you get:

```php
[
    "name"   => "Matthew",
    "status" => "online",
    "url"    => "https://mwop.net"
]
```

In the second example, I'd select all rows, including the header, and use `|` as the search.
This results in:

```markdown
| Name       | Status  | URL             |
| ----       | ------  | ---             |
| Matthew    | online  | mwop.net        |
| An Example | offline | example.com     |
| API        | online  | api.example.com |
```

> #### Hint: use your tab key
>
> The Tab key is your friend, both in vim's command mode, as well as in your shell.
> I generally type `:Tabu[Tab]`, and it expands to `:Tabularize` for me.
> And, of course, you could bind it to a keystroke or function if you wanted.

### However...

Interestingly, I use tabular less and less.

Why?

My coding standard tools can do code alignment, and since I have to run this anyways, I can save myself some time by just _not_ doing alignment during my coding session.

With markdown tables, my current markdown plugins and language server configuration autoindents for me.
As I finish a row, it automatically reformats the table to tabularize everything, which means I don't need to take any extra steps to format.

### Final Thoughts

I keep this plugin around because when I _do_ need it, it's super convenient, less error-prone than doing it manually, and saves me time.
