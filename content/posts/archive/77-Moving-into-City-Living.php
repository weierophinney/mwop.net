<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('77-Moving-into-City-Living');
$entry->setTitle('Moving into City Living');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1117990800);
$entry->setUpdated(1117990863);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
));

$body =<<<'EOT'
<p>
    We did it... we moved, again.
</p>
<p>
    However, unlike our previous two moves, which were interstate, this time we
    stayed in the same state. The same county, even. What made (makes; we're
    still finishing up as I write this) this one so jarring is the fact that
    we're going from the rural mountainside to the fourth floor of a new
    apartment/condo building adjoining an interstate spur.
</p>
<p>
    Why would we do this?
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    First, some history. In case you haven't been reading the blog, Jen and I
    are having another baby. And our one-and-only car died a few months ago
    (which we've since replaced). And we were living in West Bolton, a good
    half-hour by car to Burlington, where we work and often play -- which means
    that we've been having a long commute each day (50 minutes, minimum, each
    way). And we've been spending far more on our rent and utilities than we'd
    like; we haven't been able to save at all.
</p>
<p>
    We got to thinking that this was not an ideal situation for us. We cannot
    afford another car (either the initial purchase or the ongoing expense of
    one), so we decided it was time to look for something closer to town. And
    then, as we did, we discovered that places were either not close enough to
    change the commute significantly, or we were going to be spending the same
    amount of money (or more) to live closer as we were to live up in the
    mountains -- and sometimes this was with less space.
</p>
<p>
    We finally found several places we liked and which were in the price range
    we were targetting, and it came down to how we felt about our potential
    landlords, and they us. And the one that was cheapest, most convenient to
    our workplaces, and still somewhat reasonably sized... is the <a
        href="http://www.citysedgevt.com/">brand-new apartment on the city's
        edge</a> we're currently occupying.
</p>
<p>
    Our landlord is very nice, and a mortgage broker for a local bank; yet, this
    is actually his first rental property. He and his wife breed some sort of
    dog, and he was quite happy to have Cuervo move in. Additionally, when we
    came to visit the location, Maeve had her tiger, Talula, with her, and he
    liked the name so much that he suggested it to his wife for a puppy name. It
    just felt like a good fit.
</p>
<p>
    Now, as I mentioned above, we've moved from the rural mountainside to the
    city: we've gone from complete quiet (apart from the peepers singing) and
    complete darkness (other than the blanket of stars in the sky) to a constant
    hum of traffic and lights that never shut off. But in the mountains, all we
    could see was the sky above and trees surrounding us; we couldn't even see
    the mountain on which we lived. The new apartment is on the top floor,
    looking west...  which gives us an excellent view of the Adirondacks and a
    portion of Lake Champlain, as well as beautiful sunsets.
</p>
<p>
    The new location poses some challenges. The place is significantly smaller
    than our place in West Bolton, and doesn't have a full basement for storage
    (obviously). Plus, we're <em>adding</em> to our family, yet we're reducing
    the number of rooms by one. And, for pete's sake, we're on the fourth floor
    -- what about when Cuervo needs to pee?
</p>
<p>
    Well, now that we're mostly moved in, I can answer some of these questions.
    The move has been difficult, but a good experience. We've taken it as an
    opportunity to simplify. Which, in a nutshell, means, "throw out unnecessary
    shit." I discovered that I had four file cabinet drawers full of old papers
    that I had absolutely no use for, nor attachment to. We took several boxes
    of books to a local charity, and untold numbers of clothes and toys. We've
    now got just about everything over to the new place, except some stuff from
    the office and some storage from the basement... and I find myself wondering
    why we ever felt we needed all the things we got rid of.
</p>
<p>
    Cuervo's settling in nicely, though she's having a little trouble
    understanding that she can't be as vocal. As it turns out, Cuervo's getting
    old. She's now seven, and in this first week of walks, I've discovered that
    in her little 'explores' up in West Bolton, she was likely walking for up to
    10 minutes, and then sitting on her butt or outright laying down for a while
    before getting up to continue. She doesn't even pull incessantly on the
    leash anymore! She's certainly very healthy and in good shape, but she
    simply doesn't have quite the energy and enthusiasm I remember from walking
    her in years past. So, as it turns out, the move is probably a good thing
    for her, too -- we interact with her more, and also can keep a better eye on
    her general health.
</p>
<p>
    I've also discovered that I like living near more people. Every day, I run
    into people, usually while walking Cuervo. I already know several people by
    name, which is several people more than I met all of last year.
    Additionally, the building was built on the edge of some property that
    contains four other apartment buildings, some of which are Section 8 --
    which means that when Maeve goes down to play at the playground, she's
    meeting kids of many ethnicities and economic backgrounds. (We met a couple
    of girls whose family emigrated from Rwanda, for instance!)
</p>
<p>
    The move has been trying in many ways, though. The sheer amount of stuff to
    do has alternately overwhelmed myself and then Jen, and we have found
    ourselves getting frustrated with each other quite often. However, the
    common experience of our 'simplifying' process has also drawn us together --
    it's a goal we both share, and for which we must both sacrifice.
    Additionally, the new place has a layout that encourages staying connected:
    the kitchen, dining room, and living room all flow into each other, and the
    bedrooms open off this main room, meaning we're always within hearing
    distance -- and often sight -- of each other. 
</p>
<p>
    So, all in all, while difficult, I feel the move has been a very good one
    for the family as a whole. Now if only I could turn off the parking lot
    lights and passing traffic when I go to bed...
</p>
EOT;
$entry->setExtended($extended);

return $entry;