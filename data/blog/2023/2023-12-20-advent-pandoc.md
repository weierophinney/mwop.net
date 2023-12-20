---
id: 2023-12-20-advent-pandoc
author: matthew
title: 'Advent 2023: Pandoc'
draft: false
public: true
created: '2023-12-20T14:32:00-06:00'
updated: '2023-12-20T14:32:00-06:00'
tags:
    - advent2023
    - markdown
    - pandoc
---
Being a fan of Markdown and text formats in general, but living and working in a society where other formats are more often used, it's convenient to be able to convert my files to formats others can use.

And there's really only one tool for that: [Pandoc](https://pandoc.org).

<!--- EXTENDED -->

### What is Pandoc?

Pandoc allows conversion between different document formats.
Most are bi-directional, though a few can only go in one direction (for instance, you can convert to PDF, but cannot convert PDF documents to other formats).

What's more, it works with unix input and output streams and redirection, which allows you to work with it in a fairly intuitive fashion (intuitive if you're comfortable with these, that is).

The mnemonics I use to remember how it works:

- `-f` means _from_ (you can also use `--from`), and refers to the **format from** which you will convert.
- `-t` means _to_ (you can also use `--to`), and refers to the format **to which** you will convert.
- In unix CLIs, `<` slurps _in_ a file
- In unix CLIs, `>` spits out a file

So the most common usage pattern is:

```bash
pandoc -f gfm -t docx < input.md > output.docx
```

Which tells pandoc to convert from **G**ithub **F**lavored **M**arkdown (GFM) to docx (the format used in Word), pulling in the file `input.md`, and creating the file `output.docx`.

### What formats are supported?

Since the supported formats will vary from version to version, and based on how you install pandoc, and what libraries or other programs are installed, it supplies a couple of actions for listing them.

To find out what input formats it understands:

```bash
pandoc --list-input-formats
```

Its counterpart for output formats:

```bash
pandoc --list-output-formats
```

### Final Thoughts

Pandoc supports a _ton_ more features than these.
It understands tex and latex, mathml, and other advanced markup formats.
For HTML and ePub, you can specify CSS, images, fonts, and more.
Formats that might have fixed widths have options that allow you to specify wrapping or even the page dimensions.

99% of the time, though, I'm just converting from one format to another, and the usage I outlined above gets me there.
Tooling like this is a large part of why I've stuck with Linux for my desktop operating system for more than two decades.
