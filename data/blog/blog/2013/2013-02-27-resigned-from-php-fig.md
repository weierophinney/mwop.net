---
id: 2013-02-27-resigned-from-php-fig
author: matthew
title: 'On PHP-FIG'
draft: false
public: false
created: '2013-02-27T12:41:00-06:00'
updated: '2013-02-28T08:22:00-06:00'
tags:
    - personal
---
*Update (2012-02-28)*
---------------------

*Based on reactions I've seen on Twitter, mailed to me, and reported to me, I
want to clarify a few points.*

- *While I do not support shared interfaces, I do understand that some people
  do. Had I stayed in the group, I would have simply voted "no" on such
  proposals. That does not mean I would be upset if they passed, simply that I
  might not choose to implement them.*
- *My leaving the group has no effect on whether or not ZF will continue to
  follow any of the recommendations. I am simply saying I will not participate
  in the development of or passing of recommendations.*
- *I am not "rage quitting," nor did I ever view the group as my personal
  fiefdom, or only useful when it had a group of like-minded developers (which
  was never the case; the original group was incredibly disparate). My argument
  is simply this: too many participating are forgetting to check their egos at
  the door so that they may work towards consensus. **As long as any arguments
  are couched in terms of "right" or "wrong," somebody is missing the point of
  the group.***

Original Post
-------------

Yesterday, I left the PHP-FIG group.

As in: left the github organization I created, and removed myself from the
mailing list I created.

I have contacted members of my development team and the Zend Framework
community review team to see if anybody is willing to represent ZF in the
group. I no longer am.

I was going to leave quietly, but as a favor to Paul M. Jones — a good friend
and sometimes collaborator — I'm writing now.

<!--- EXTENDED -->

I had high hopes for the group. It was the culmination of something I've been
ruminating on for almost a decade ([see post number 12 on my blog, dated to January 2004, for proof](http://www.mwop.net/blog/12-PHP-standards-ruminations.html)).
My thoughts have mainly been around coding standards and best practices,
helping educate developers around their benefits, and how to leverage both in
order to create maintainable code.

First, a few thoughts:

- I personally feel that interfaces are a bad fit for the organization; [I have outlined my thoughts on interface standardization elsewhere](http://www.mwop.net/blog/2012-12-20-on-shared-interfaces.html).
- Multiple coding standards are okay. A standard for every project,
  organization, or developer is not. The ideal is a handful or so for any given
  language, as more than that means there are no standards; it's just a
  free-for-all.
- No individual coding standard will satisfy all developers. In fact, in my
  experience, there will always be choices in *any* standard that even the
  authors of the standard are unhappy with. The point of a coding standard is
  not to make everyone happy. It's to have a document that details the
  structure of code so that developers focus on the intent of code, not the way
  it's formatted.
- Coding standards are useless if they do not contain hard rules, as automated
  tooling to sniff for CS issues cannot be written. "COULD", "SHOULD", and
  "CAN" are all problematic, and any use of "EITHER" is going to make
  automation ridiculously complex.
- In the end, no matter what the technical arguments are for any given detail,
  all coding standards are ultimately subjective. The only objective standard
  is what is parseable in the given language.
- What matters is that you *adopt* an *existing* standard, and *use* it. When
  you do, you can automate some code review, prevent developer commit
  skirmishes arising from differences in formatting aesthetics, and focus on
  the problem you're trying to solve in your code.

With those thoughts as background, then, I can better explain my departure.

The point of PHP-FIG was to create consensus around practices shared by its
member groups, no more, no less.

When PSR-0 was created, we had around a half-dozen member groups. You have to
start somewhere.

Each proposal since then has had an increasing number of members both
discussing and voting on proposals. That means the early proposals may not be
representative of the later membership. That's a simple fact.

That does not mean the standards should *change*. Once published, a standard is
done. The only thing that can happen is that a *new* standard may be created
that can *supersede* an existing standard, or be used *instead* *of* an
existing standard. As examples from existing standards bodies, consider RFC
822, which codified the format of internet text messages (email); it superseded
at least one other RFC, and has itself been superseded twice (in RFC 2822 and
RFC 5322).

PHP-FIG adopted the same workflow. If new practices emerge, or the makeup of
the organization significantly changes, and existing recommendations are found
to be obsolete or outdated, a *new* recommendation may be proposed to supersede
or be adopted in parallel to them.

Parallel standards from the same body, however, should be considered *very*
carefully, as they lead to splintering and fragmentation of the standards body
and member organizations. If consensus cannot be achieved, why bother?

What I see happening in the PHP-FIG github organization (in pull request
comment threads) and google group, however, is the exact opposite of the goals
that originally led to the group being formed. Instead of people trying to
achieve consensus, I see a lot of polarizing, all-or-nothing arguments
occurring, often over very subjective things. Developers are defending their
opinions and viewpoints with little to no real analysis of what others present.
I see a lot of "you're wrong" types of remarks. These are coming largely from
non-voting members, but it means the signal-to-noise ratio within these forums
is skewed; it's hard to find reasonable discussion occurring due to this
behavior.

The "old guard" is guilty of this at times, too — but not as often as many
might insist. The insistence of folks like Paul M Jones, Paul Dragoonis, and
Lukas Kahwe Smith that discussion should happen on-list, and that PRs are not
considered for existing, accepted standards is actually reasonable — for the
reasons I outlined above. A standard is written once; if it needs revision, a
*new* standard should be written superseding it.

What is most irritating, however, is it doesn't matter how many times such
statements are made; people insist on debating their pet peeves on existing
standards without following the guidelines and established process — nor
listening when others point out that the debate has occurred before, will occur
again, and needs to stop for now so that we can focus on constructive tasks. I
see a lot of name calling, a lot of accusations of a "dictatorship" (they're
*recommendations* people; they're not *requirements*), and overall egotistical
behavior.

I'm tired of it. I have better things to do with my time, things I want to
create, software I want to support, hobbies and interests I want to pursue.
Debating brace placement, tabs vs spaces (for the umpteenth time), or whether
or not annotations have a place in programming in a dynamic language? Not so
much.

I hope PHP-FIG can achieve the goals it started with. It will have to do so
without my participation, though.

#### Note

I've disabled comments on this post.
