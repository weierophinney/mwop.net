<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('35-Scrap-that.-Were-gonna-use-PHP');
$entry->setTitle('Scrap that. We\'re gonna\' use PHP');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1080519852);
$entry->setUpdated(1095702899);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'perl',
  2 => 'personal',
  3 => 'php',
));

$body =<<<'EOT'
<p>
    I've been researching and coding for a couple months now with the decision
    that I'd rewrite the family website/portal using mod_perl with
    CGI::Application. I still like the idea, but a couple things recently have
    made me rethink it.
</p>
<p>
    For starters, the perl DBI is a bit of a pain to program. At work, I've
    become very accustomed to using PEAR's DB library, and while it's in many
    ways derived from perl's DBI, it's much simpler to use. 
</p>
<p>
    Then there's the whole HTML::Template debacle. There's several ways in which
    to write the templates, but they don't all work in all situations, and, it
    seems they're a bit limited. We've started using PHP's Smarty at work, and
    it's much more intuitive, a wee bit more consistent, and almost infinitely
    more extendable. I could go the Template::Toolkit route for perl, but that's
    almost like learning another whole language.
</p>
<p>
    Then, there's the way objects work in perl versus PHP. I've discovered
    that PHP objects are very easy and very extendable. I wouldn't have found
    them half as easy, however, if I hadn't already been doing object oriented
    programming in perl. One major difference, however, is how easy it is to
    create new attributes on the fly, and the syntax is much easier and cleaner.
</p>
<p>
    Add to that the fact that if you want to dynamically require modules in
    perl, you have to go through some significant, often unsurmountable, hoops.
    So you can't easily have dynamic objects of dynamically defined classes. In
    PHP, though, you can require_once or include_once at any time without even
    thinking.
</p>
<p>
    The final straw, however, was when I did my first OO application in PHP this
    past week. I hammered it out in a matter of an hour or so. Then I rewrote it
    to incorporate Smarty in around an hour. And it all worked easily. Then I
    wrote a form-handling libary in just over two hours that worked immediately
    -- and made it possible for me to write a several screen application in a
    matter of an hour, complete with form, form validation, and database calls.
    Doing the same with CGI::Application took me hours, if not days.
</p>
<p>
    So, my idea is this: port CGI::Application to PHP. I <em>love</em> the
    concept of CGI::App -- it's exactly how I want to program, and I think it's
    solid. However, by porting it to PHP, I automatically have session and
    cookie support, and database support is only a few lines of code away when I
    use PEAR; I'll add Smarty as the template toolkit of choice, but make it
    easy to override the template methods to utilize . I get a nice MVC-style
    application template, but one that makes developing quickie applications
    truly a snap.
</p>
<p>
    This falls under the "right-tool-for-the-job" category; perl, while a
    wonderful language, and with a large tradition as a CGI language, was not
    developed <em>for the web</em> as PHP was. PHP just makes more sense in this
    instance. And I won't be abandoning perl by any stretch; I still use it
    daily at work and at home for solving any number of tasks from automated
    backups to checking server availability to keeping my ethernet connection
    alive. But I have real strengths as a PHP developer, and it would be a shame
    not to use those strengths with our home website.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;