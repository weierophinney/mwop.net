<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('59-Enter-the-Matrix');
$entry->setTitle('Enter the Matrix');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1111379621);
$entry->setUpdated(1111379779);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
));

$body =<<<'EOT'
<p>
    I couldn't resist... the car model demands it...
</p>
<p>
     For those not familiar with where I live, my family and I live in West
     Bolton, VT -- about 20 miles from Burlington, and at the base of Bolton
     Mountain. Our daily commute is 4 miles on a dirt road, another 3 to 4 miles
     on some twisty two-laners at 35mph to the interstate, and around 10 miles
     on the interstate into Burlington. Then there's all the miles in town
     getting Maeve to day-care, Jen or myself dropped off, and whomever has the
     car to work. And we only have one car.
</p>
<p>
    So, you can imagine the crisis when, almost a month ago, our Toyota Rav4
    died on the way in to work.
</p>
<p>
    We started it up that day, and it had this funny knocking sound. I
    remembered a similar sound in my old pickup back in Montana... the one that
    died. I determined to get it into a shop that day to get it diagnosed. The
    noise came and went while we were on the backroads, and because it wasn't
    constant, I figured it couldn't be too serious.
</p>
<p>
    And then we tried to get to highway speeds.... a few miles on the
    interstate, and it was evident we were in trouble. The Rav was having
    trouble maintaining 60mph on the way up French Hill -- when it normally was
    able to accelerate past 70mph. And the knocking sound was getting worse and
    louder.
</p>
<p>
    We resolved to pull off at the first exit, at Tafts Corners in Williston. I
    pulled into the first gas station there, and as we tried to find a place to
    park the vehicle, a mechanic was flagging at us to stop the car. He came
    over to where we parked and said, "Sounds like you've blown your engine."
</p>
<p>
    These, of course, were the absolute last words I wanted to hear.
</p>
<p>
    To make a long story short, apparently a bearing was thrown when we started
    the engine that day, and because we decided to drive it, we basically
    destroyed the engine. The cost to replace it: around $6,000.
</p>
<p>
    Now, we're not exactly what you'd call "financially secure". We've had a lot
    of transitions in the past five years, and except for the past year and a
    few months, haven't typically both been working at the same time. We've been
    in a perpetual cycle of having enough to pay the bills... but having to pay
    consistently late. And we haven't been able to do much, if anything, about our
    educational debt. In short, our credit sucks. Which means that $6,000 is a
    big deal.
</p>
<p>
    Did I mention that, at the time of the incident, we still had 17 months left
    on our car payments?
</p>
<p>
    And, on top of it, I've been in the middle of a <em>huge</em> project for
    work that's required a fair bit of overtime -- and very little wiggle room
    for personal time?
</p>
<p>
    The timing could not have been worse, either professionally or financially.
</p>
<p>
    We've been very fortunate, however. Jen's parents very graciously offerred
    to pay off our existing car loan -- which helped tremendously. It bought us
    both the time to figure things out, as well as eliminated one factor that
    may have barred our ability to borrow towards repairs or a new car.
    Additionally, a friend of Jen's turns out to be absolutely ruthless when it
    comes to dealing with car salespeople, and went to bat for us in working out
    a deal. If it hadn't been for her efforts -- and those of the salesperson,
    who also went to bat for us -- we would not have gotten more than a thousand
    or so for the vehicle; we ended up getting over $3,000 for it, as is.
    Finally, the finance guy at the dealership advocated for us tremendously so
    we could get a loan on a new vehicle, with the Rav as our trade in.
</p>
<p>
    So, to conclude: We're now proud owners of a 2005 Toyota Matrix! (And now
    the mystery of the title is revealed... to all you Matrix fans out there...)
</p>
<p>
    I'll try to get a photo of the car up soon... about the time we update the
    year-old photos on our site... :-)
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;