---
id: 2023-12-19-advent-vim-markdown
author: matthew
title: 'Advent 2023: (n)vim Plugins: vim-markdown'
draft: false
public: true
created: '2023-12-19T12:14:00-06:00'
updated: '2023-12-19T12:14:00-06:00'
tags:
    - advent2023
    - markdown
    - neovim
    - nvim
    - vim
---
I'm a huge fan of [Markdown](https://www.markdownguide.org/).
There's something elegant in using textual sigils to provide contextual information.
I've used it for taking notes, creating RSS feed content, producing my blog, and even in emails (I soooo wish there were a way to convert markdown within Outlook for the web and GMail!)

So it should come as no surprise that I use a variety of tools to help me when writing markdown in (n)vim.

<!--- EXTENDED -->

### What are the tools?

The [aareman/vim-markdown](https://github.com/aareman/vim-markdown) plugin provides the core functionality, and features syntax highlighting.
What's great is that in a terminal that supports it, you'll actually see things appear in bold, italics, or underlined.
Headers, links, and monospace text each get a different color, and sometimes even weight.

From there, I have a number of other plugins that augment the functionality from vim-markdown:

- [vim-table-mode](dhruvasagar/vim-table-mode) provides on-the-fly autoformatting of textual tables, including those in Markdown.
  This is invaluable, as it allows me to see _as I type_ if I'm missing columns, adding extra columns, etc.
- [img-paste.vim](https://github.com/img-paste-devs/img-paste.vim) allows pasting a screenshot into a markdown document.
  It will copy the screenshot to a specified relative directory, and then create the markup for displaying it in the document.
- [vim-pandoc-syntax](https://github.com/vim-pandoc/vim-pandoc-syntax) does much of the heavy lifting for syntax highlighting, particularly when it comes to fenced code blocks.
  If you use [Github Flavored Markdown](https://github.github.com/gfm/), you can optionally specify a _language_ when creating a fenced code block; vim-pandoc-syntax will identify the language, and use it to provide syntax highlighting within the code block for the given language.

Additionally, with [coc.nvim](/blog/2023-12-17-advent-vim-coc.html), I get integration with [markdownlint](https://github.com/markdownlint/markdownlint), which flags potential syntax and style issues.

### How I configure it

I configure vim-markdown using the following:

```vimrc
let g:markdown_disable_folding            = 1
let g:markdown_disable_motions            = 0
let g:markdown_disable_spell_checking     = 0
let g:markdown_disable_conceal            = 0
let g:markdown_disable_table_mode         = 0
let g:markdown_disable_pandoc_integration = 0
let g:markdown_disable_clean_empty_on_cr  = 0

nnoremap <buffer> <Leader>x :call markdown#SwitchStatus()<CR>
```

These do the following:

- I rarely use code folding, and when I do, I want to do it manually.
  As such, I disable auto-folding done by the plugin.
- vim-markdown provides a variety of _motions_ that let you jump around more quickly in a markdown document, particularly to the previous header (`[[`) or the next header (`]]`).
  I want to keep these around.
- Spell checking is nice.
- "conceal" will _conceal_ markup sigils when you're not on a line; the terminal continues to highlight them appropriately (e.g. bold, italic, underlined, etc.).
  Links are collapsed to just the highlighted linked text, collapsing the content around them.
  Doing this makes _reading_ the document easier; when you're on the line, it will reveal all concealed characters so you can edit.
- Table mode enables the integration with vim-table-mode; I definitely want this.
- Pandoc integration enables the pandoc syntax highlighting; I definitely want this.
- If I hit Enter from a list item, it creates a new list item.
  This last setting means that I can hit Enter again, and it will clear the list item and start a line below.
  It's an easy way to end a list and start typing the next paragraph.
- I often use GFM checkboxes for TODO lists. The mapping declaration maps `<Leader>x` to toggle the checkbox.

### Final thoughts

After [vim-surround](/blog/2023-12-15-advent-vim-surround.html) and [coc.nvim](/blog/2023-12-17-advent-vim-coc.html), this is undoubtedly the plugin that gives me the most value.
I benefited from it _typing up this very blog post_!
While a GUI IDE can provide side-by-side editing where you can see how the Markdown is transformed, I rarely need that; the whole point of Markdown is to provide a _human readable_ format that provides _contextual markup_.
Reading the raw file should be enough.

vim-markdown gives me the ability to have a raw Markdown file, while simultaneously giving me just enough visual context to understand things even when skimming a document.
