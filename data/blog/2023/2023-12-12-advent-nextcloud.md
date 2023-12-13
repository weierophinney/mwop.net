---
id: 2023-12-12-advent-nextcloud
author: matthew
title: 'Advent 2023: Nextcloud'
draft: false
public: true
created: '2023-12-12T15:18:15-06:00'
updated: '2023-12-12T15:18:15-06:00'
tags:
    - advent2023
    - nextcloud
---
Halfway through advent; half to go!

This will be a short post, to detail an invaluable tool I've been using for around 5 years now: [Nextcloud](https://nextcloud.com).

<!--- EXTENDED -->

### What is Nextcloud?

Nextcloud competes with the likes of Google Workspace or O365.
At its heart, it provides file syncing, but then offers a suite of other tools as well: calendaring (including shared calendars), RSS reader, contacts, photos, notes, music, Kanban boards, recipes, tasks, and and and and and

It's a comprehensive suite of collaboration tools, essentially.

But better: it's open source, and relatively easy to self-host.

I've been self-hosting for most of the 5 years I've been using it.
I use the [snap](https://snapcraft.io) option, which is amazing: it self-updates, ensuring I'm not running out-of-date software at any point.
I've literally had one issue in all that time, and it was something I was able to recover from within minutes.

Being able to self-host is awesome, as it means I keep things like my contacts, calendar, and shared files _to myself_.
They're not being scanned to provide better advertising for a company, or to train an AI.
I've even got my Android phone syncing to and from my Nextcloud instance, instead of using Google Drive and the various contact and calendar syncing services Google provides.
The Nextcloud app on my phone auto-syncs photos as I take them, so I know they'll always be somewhere I can access them — and not feeding the next AI image generation app.
I use it daily as my RSS reader, and the Nextcloud News app on my phone is one of the most-used I have.

The system is extensible, and there's a marketplace of add-ons and extensions should you need additional functionality.
And since it's written in PHP... I could contribute or write extensions should I need or want to.

Are there things that _are not_ great about it?
Definitely.
I've not had great experiences with the Notes functionality, and have instead moved to using [Logseq](/blog/2023-12-01-advent-logseq) — but I sync my Logseq notes _using_ Nextcloud, which works brilliantly!
(In fact, I use Syncthing to sync from Nextcloud to my iPad as well!)
The recipes app seemed promising, but I found the display and search slow, and the input was cumbersome (I use Logseq for recipes now.)

Most of the issues I have had are likely due to not hosting on a capable enough machine; that's on me.
(I updated the machine to give it more memory, CPU, and storage this summer, and I've noticed a significant difference in the apps I _do_ use.)

### Final Thoughts

Are you sick of "being the product" by using free cloud storage and services?
Do you want own your data?
I honestly can't recommend enough spinning up your own Nextcloud instance.
(And if you don't want to manage it yourself, there are a variety of hosting providers that offer it as a SaaS; you get the data privacy you deserve, but without the hassle of managing the system.)
