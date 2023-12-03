---
id: 2023-12-01-advent-logseq
author: matthew
title: 'Advent 2023: Logseq'
draft: false
public: true
created: '2023-12-01T23:59:59-06:00'
updated: '2023-12-02T23:59:59-06:00'
tags:
    - advent2023
    - logseq
    - pkm
    - vim
---

In years past, folks across a variety of programming languages have organized Advent events in December, to highlight different tools, different frameworks, different programming practices, and more, often inviting guests to author each post.

I thought I'd try an experiment: I've had a ton of ideas for blog posts, many of them short, and just... never write them.
What if I were to do a personal advent, and write these up?

Let's see how far I get.

Today's topic: [Logseq](https://logseq.com).

<!--- EXTENDED -->

### What was I trying to solve?

For many years (over a decade), I kept a little knowledge base of markdown files.
I had a `diary` command that would open a file named after the current day's date, creating it if it didn't exist, in [vim](https://www.vim.org).
I'd create todo lists, notes from meetings, ideas I wanted to investigate, even blog and email drafts.

When I needed to find something, I'd use a tool such as [ack](https://beyondgrep.com) to search through all files.

It worked, but I noticed a lot of things:

- For a long time, I'd have a TODO list for the day, and if I didn't finish items, I'd copy them to the next day's file.
  This was tedious, though at one point I created a script to make it easier.
  But worse: I discovered that the list would grow and grow, and lead to anxiety when I didn't finish them.
  (Occasionally, I'd toss items off the list, in recognition that I'd likely never do them.)
- Searching worked, but it was error prone.
  I'd often forget to make search case insensitive.
  Or the terms would return too many items.
  Because it was regular expression based searching, there was no way to say "find all files with this search term that also contain this other search term, but not necessarily in any proximity to each other."
- It was great while I was on a computer, but next to impossible to use on a mobile or tablet.
  As such, I tended to use it for work or for organizing programming projects, but not for the day-to-day.
  And there's a lot I want to be able to come back to, much of it when I'm not at my computer: recipes, lists of things to watch and read, art inspiration, shopping lists, and more.
  So having a file-based solution was not going to work.

I tried a few different things over time:

- Evernote.
  I actually used this for a couple of years, mainly to capture bookmarks, but cancelled it quite some time back (likely around 2017) when I realized I was getting locked into an ecosystem, and one that was increasingly moving away from how I actually wanted to use it.
  And the fact that it didn't really allow linking between notes, or have a vim mode made it something I wouldn't open regularly.
- Google Keep.
  This was installed by default on my Android phone, and the data went with me... but it was difficult to use it on the computer, and the search for it was really spotty, which was surprising for a company that started as a search engine.
- [Nextcloud Notes](https://github.com/nextcloud/notes).
  When I started using this, I imported my markdown files... and my instance was too underpowered to handle the sheer volume of notes I had.
  On top of that, once I got the notes imported to Nextcloud, the Android app would choke trying to sync, and was super slow when I would try to use it.
  And search... was not great.
- [Joplin](https://joplinapp.org).
  This is an open source app that works on top of either Nextcloud Notes, or just on a filesystem.
  Again, I struggled with the sheer number of notes I had, and the fact that I couldn't use it on mobile made it not a great fit for me.
- [nb](https://xwmx.github.io/nb/).
  Somebody on Mastodon recommended this a little over a year ago, and I dove in as it was just markdown, vim, and the CLI. While it's text centric, it also has things like search and todo lists and more... but this was also it's hugest limitation: I couldn't feasibly use it on my phone or from my tablet.
  On top of that, the way that lists worked, and the fact that if you wanted project-specific lists you essentially had to have separate collections, meant that I stuggled to use it regularly or stick with it.

I've basically limped along, hopping from one solution to the next, without fully settling on any due to limitations.

### What is Logseq?

Logseq is a [Personal Knowledge Manager](https://en.wikipedia.org/wiki/Personal_knowledge_management) (PKM) application.
PKM tools provide a way to collect information, and, more importantly, surface it.
There's been a proliferation of these tools in the past years; you may have heard of [Obsidian](https://obsidian.md), [Notion](https://www.notion.so), and [SimpleNote](https://simplenote.com).
But the idea is not new; things like [Org Mode for emacs](https://orgmode.org) have existed for ages, and some PKM ideas derive from the [Zettelkasten method](https://zettelkasten.de/introduction/).

Logseq itself is an open source application, built using Node and running in Electron; as such, it's cross-platform (I've used it on Linux, Android, iOS, and Windows).
It stores the actual content as [Markdown](https://www.markdownguide.org), though it uses tabs instead of spaces for indentation, and every line is a bullet point (more on this later).
Because it uses local file storage, you can use [git](https://git-scm.org) to version files and sync between systems, or you can use any file syncing you have available.
(I've used [Syncthing](https://syncthing.net), [Nextcloud](https://nextcloud.com), and even OneDrive at work.)
The project rolled out a beta of a sync-as-a-service offering this past year, but I appreciate that it's not necessary or required in the least in order to use it.

### How does Logseq work?

I started using this in late December or January of this past year, and was quickly surprised by what it enabled.

Let me explain.

When you open Logseq, you start in the **Journal**. This is a daily record, and once the day rolls over, it starts a new one.
99% of my work is done in the journal, and this is _fantastic_.
It allows me to understand _when_ I learned something, had a meeting, or recorded a task.
While you _can_ create separate pages, most of the stuff I want to track is relatively ephemeral.

Next, you can **tag** pages.
Let's say I'm writing a note, and it's related to PHP; I can type `#php`, and this becomes a link.
If I click that link, it takes me to the page — which may or may not exist.
But even if it does _not_ exist, it will reference all the pages that link to it.
This is a cheap and easy way to organize things, and if I later want to add notes specific to that page, I can; it then becomes a first-class page in the system.

Tags are done as **metadata**, and metadata can be applied at the page level or item level.
Some, like `tags:: {tag list}` and `due:: {date}` are special to the system, and will surface functionality.
But they can be anything.
I've used them to be able to note page or item _types_, people I have meetings with (which I link to the pages for each person), and more.

You can also create **templates**.
These are text snippets that you want to re-use.
As examples, for work, I created templates for meetings (to allow me to indicate who the meeting was with,
when it happened, the agenda, any notes I took, and any action items from it), and for weekly prioritization (more on that later).
At home, I created templates for bookmarks (so I could give information on _why_ I found the bookmark useful, the title of the page, and related tags), and recipes (so I could tag the type of food, specific ingredients, etc.).

Tagging pages makes it easy to **search**.
I can search, and if there's a page matching the keyword, I can go to it and immediately see what other notes I've taken on that subject.
Even better, though, Logseq includes both some basic search functionality that allows selecting for things like tags/pages, attributes (such as whether or not something is a task), and free text, and this can be done _within the page itself_, allowing you to see what matches immediately.

You can also create **slash functions** that pull in templates or evaluate to text.
An example I use frequently is one I installed from the Logseq marketplace, which creates a search for tasks completed in the past week.

All of this brings me to another cool feature.
I noted in the description of Logseq that each line in a page is a bullet point.
Logseq creates hashes for each of these, which allows referencing them.

This means you can:

- Link to any page or _item in a page_.
- Embed items _anywhere_.
  To do this, you right click on the bullet for the item, which allows you to copy a block embed; you then paste this in another page, and you see the content _right there_, and can even manipulate it from there!

Because of all this functionality, Logseq _surfaces relevant information when you need it_.

Did you _schedule_ something in the future?
If so, the Journal will show you upcoming scheduled items — not all of them, but the ones coming up _soon_ (which you can define in the configuration).
You can create Journal pages for dates _in the future_, and if they include todo items... those will show up on the Journal as well, so you can remember.
I often create Journal pages ahead of time to create agendas for my one-on-ones; this ensures that those fleeting thoughts of "I should ask them about so-and-so" don't get forgotten when the day of the meeting rolls around.

### How do I use Logseq?

#### Prioritization

My prioritization template for work brings in two search queries, one for pages with a _project_ type that are _incomplete_, another for _incomplete tasks_ that are not part of a project, which represents my backlog.
This allows me to go through each week and identify what on the backlog I can and/or should work on in the coming week, cancel tasks I recognize I will no longer do or which are no longer relevant, mark things as done that I may have completed this past week without realizing a task existed, and so on.

What I do is go through these items, and choose what I want to work on.
I then _embed_ those items into the page for that week, which gives me a list _without duplication_, and which I can then reference later when I'm wanting to understand what I did that week.
I often also _schedule_ items at that time, to ensure they surface on my journal pages.

At the end of the week, I run the query to show what I completed the past week on that prioritization page.
This gives me an excellent reference of what I _actually_ did — which is often far more than I'd planned on for the week!
Often, if I find I did not finish what I set out to do that week, I can tell at a glance _why_: an unexpected request from my GM, a request from marketing, additional customer calls, etc.

These features have allowed me to be more intentional in my interactions with co-workers, ensure I'm more accountable to myself and to others in ensuring I get work done, and allow me to be better organized.

#### Home cookbook

I've tried a number of solutions for recipes in the past, including building my own apps.

The problem is (a) maintaining those apps, and (b) being able to find recipes when I need them.

For my personal Logseq instance, I created a template for recipes so that they are in a common structure, and that template includes metadata and tags so I can (a) find the original source if I need it, and (b) search for recipes by ingredients I commonly might have on hand.

I've got everything in it from recipes and tips for salting, brining, and seasoning, to main dishes, to cocktails.

Want to do a Rum-based cocktail?
Search for `(and [[rum]] [[cocktail]])`.
Need the instructions for brining and roasting the annual turkey?
Search for "turkey".

It's been magical.

#### Weekend projects and household todos

I often know that I need to do something around the house, but forget by the time the weekend arrives.
I now create these as tasks in Logseq, with the tag "weekend".
I can surface them quickly, and even use the ability to embed links to items to create a todo list for the weekend!

### Final words

Would I recommend Logseq?

Wholeheartedly!

Look, it's not perfect.
The Markdown it uses is tab-delimited, and each line is a bullet point, which makes using it to draft blog posts or other Markdown a bit of a pain.
(I'm planning to write a tool to convert to standard Markdown over my holiday break, though!)
But it _is_ Markdown, and it even supports things like blockquotes and fenced code blocks and tables, which gives a wealth of ways to format text.

On top of that, it has a rich plugin architecture and ecosystem.
You can do whiteboards, embed drawing widgets (I created an architectural diagram I shared with my engineering team using one of these!), create Kanban boards, or even use it as a full-featured calendar.
One I use extensively is a plugin giving Vim bindings, which allows me to move around and edit in ways that are relatively familiar.
If I absolutely have to, I can edit the files by hand (though with the slight differences in Markdown, this can sometimes introduce issues).

The fact that it is open source also means I can use it in perpeituity, so long as I am able to compile it and/or find an Electron version it works with.
And because it's using plain text and SQLite under the hood, I can take the data and use it however I want.

Is it the right tool for _you_?
I don't know.
I think the important thing is to try one of these tools and _stick with it_.
More and more functionality and content surfaces for me the longer I use the tool, and this is true of any good tool, I've found.
(I'm still learning things in vim to this day, and it's been 23 years of use to me!)
