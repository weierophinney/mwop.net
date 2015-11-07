<?php // @codingStandardsIgnoreFile
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2015-06-08-php-is-20');
$entry->setTitle('PHP is 20!');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2015-06-08 08:15', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2015-06-08 08:15', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  '20yearsofphp',
  'php',
  'programming',
  'zend',
));

$body =<<<'EOT'
<p>
  Today, 20 years ago, <a href="https://groups.google.com/forum/#!msg/comp.infosystems.www.authoring.cgi/PyJ25gZ6z7A/M9FkTUVDfcwJ">Rasmus
  Lerdorf publicly released PHP</a>. <a
  href="http://benramsey.com/blog/2015/06/20-years-of-php/">Ben
  Ramsey has issued a call-to-action for people to blog the event</a> and the
  impact PHP has had on their lives and careers; this is my entry.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
  In 2000, the small publisher I worked for as a graphics technician<sup><a
  id="ref-0" href="#fn-0">0</a></sup> built a new
  office. The owners decided this was a good chance to upgrade the networks and
  bring our small website in-house. Since I was the only person in the office
  with any real technical background (having minored in Mathematics in college,
  with 2 years of CS courses), I was tagged for the job. 
</p>

<p>
  The mail-order<sup><a id="ref-1" href="#fn-1">1</a></sup> software we used was built on
  <a href="http://uploads.mwop.net/2015-06-08-WiaN.jpg">FoxPro</a>, and had a
  plugin module that could expose the catalog via IIS; one thing the owner was
  excited about was that we'd finally have real-time updates, instead of waiting
  up to a week for our hosting provider to sync inventory.
</p>

<p>
  Of course, once we had this in-house, the owner also wanted customizations.
</p>

<p>
  The site was an interesting hodge-podge of HTML and ASP<sup><a id="ref-2" href="#fn-2">2</a></sup>.
  I learned enough HTML, CSS, JavaScript, and ASP to <strike>be
  dangerous</strike> customize the site, and the owner was happy. For a month or
  so. And then they noted that there was a new, national competitor in the book
  order business, a certain company starting with the letter "A" that is a
  behemoth today (heck, I'm hosting my site on their services!), and that this
  company offered user-submitted book reviews; we <em>must</em> offer those, he
  said.
</p>

<p>
  I was going to try and write it in ASP, but I stumbled on setting up new
  database access credentials. I then discovered that IIS allowed adding new
  modules for new languages, and discovered I could add Perl, which was a
  language I'd encountered at a previous job, and for which there were tons of
  free tutorials. So I installed it, and started creating our review system.
</p>

<p>
  And discovered that, despite my resolve during college to never program again,
  I actually really, really enjoyed problem solving using computers.
</p>

<p>
  The timing was excellent. A few local internet companies were growing at a
  huge pace, and consistently had openings for developers. I noticed they were
  both advertising for "PHP" developers, so I decided to start learning that. I
  picked up this book to get me started:
</p>

<p>
  <img src="http://uploads.mwop.net/2015-06-08-WiaN.jpg" alt="Webmaster in a Nutshell" width="600"/>
</p>

<p>
  "Webmaster." What a quaint term today, but back then, it was a magical
  profession. That book contained tons of information on HTML, CSS, JavaScript,
  Perl, and this new-fangled language, PHP, which was taking the industry by
  storm. The book was mainly a technical reference, containing listings of
  functions with their expected arguments and return values, but it was enough.
  I started writing a webmail client as a personal project, something to teach
  me the language. A few months later, I landed a job, and I've been programming
  in either PHP or Perl ever since.
</p>

<p>
  In the early years, I was active in newsgroups<sup><a id="ref-3" href="#fn-3">3</a></sup>,
  particularly php-general, at first asking questions, but later answering them.
  In 2004, I started blogging, and, as a result, networking with peers around
  the world; it was incredibly rewarding to share problems and solutions with
  so many talented developers.
</p>

<p>
  Sometime in early 2005, I received an email from a man named Daniel Kushner at
  Zend, offering me a voucher to take the Zend Certified Engineer exam, which
  Zend was just launching. I was suprised and flattered to be contacted; the
  idea that I'd be on the radar of anybody at Zend was beyond belief! I ended up
  not using the voucher, however, as my company sent me to my first conference
  that spring, PHP Tropics<sup><a id="ref-4" href="#fn-4">4</a></sup>, where I
  took the exam.
</p>

<p>
  Back then, many of the venues that offered the exam had to do so with the old
  fill-in cards, and were hand-scored. As a result, examinees had to wait weeks for
  results; in our case, we waited two months! I was terribly excited to get my
  certificate, but even more surprised and astonished when two days later,
  Daniel Kushner contacted me to schedule an interview for a position at Zend!
</p>

<p>
  That position was to be as "Editor-in-Chief" of Zend's <a
    href="http://devzone.zend.com/">DevZone</a>, which they were just launching.
  I was woefully underqualified, and after my in-person interview, essentially
  wrote off the job and chalked up the interview as an interesting life experience.
</p>

<p>
  Three weeks later, Daniel called me up to tell me they wouldn't be hiring me
  for the EiC position, but that he did want to offer me a position as a PHP
  Developer on his new "eBiz" team. I leapt at the chance.
</p>

<p>
  That was ten years ago.
</p>

<p>
  I literally owe my career to PHP. So, thank you to everyone who has ever
  contributed to the language; it's been a wonderful journey!
</p>

<h4>Footnotes</h4>

<ul>
  <li id="fn-0"><sup><a href="#ref-0">0</a></sup> Yes, you read that
    correctly. I drew maps for hunting and fishing guidebooks, and did book and
    catalog layout.</li>
  <li id="fn-1"><sup><a href="#ref-1">1</a></sup> For those of you too young to remember,
    "mail-order" was what we did before the internet. You either filled out an
    order form on paper, by hand, and mailed it via postal service to the
    fulfillment agency, or you would call them and place the order with them.</li>
  <li id="fn-2"><sup><a href="#ref-2">2</a></sup> Not ASP.NET, but its granddaddy, what
    we often term "Classic ASP"; this <em>was</em> the year 2000, after all!</li>
  <li id="fn-3"><sup><a href="#ref-3">3</a></sup> Newsgroups, which were
    offered over NNTP, were a staple of the early internet. Users could log in
    and post messages anonymously or with a handle, and check back later for
    responses. If you registered with your email address, you could even post
    and receive replies via email. You know, similar to Google Groups
    today.</li>
  <li id="fn-4"><sup><a href="#ref-4">4</a></sup> I was supposed to go to PHP
    Quebec, but we were launching a major project just two days before the
    conference, and nobody felt it was a good idea for the team to be
    out-of-office that soon after launch. So management promised to send us to
    the very next conference. The fact that we went to a conference at an
    all-inclusive resort in Cancun did not earn us any friends at the office
    that year!</li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;
