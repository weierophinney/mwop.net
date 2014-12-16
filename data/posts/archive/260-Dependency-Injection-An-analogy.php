<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('260-Dependency-Injection-An-analogy');
$entry->setTitle('Dependency Injection: An analogy');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1300744335);
$entry->setUpdated(1301034313);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'oop',
));

$body =<<<'EOT'
<p>
I've been working on a proposal for including service locators and dependency
injection containers in Zend Framework 2.0, and one issue I've had is trying to
explain the basic concept to developers unfamiliar with the concepts -- or with
pre-conceptions that diverge from the use cases I'm proposing.
</p>

<p>
In talking with my wife about it a week or two ago, I realized that I needed an
analogy she could understand; I was basically using her as my
<a href="http://en.wikipedia.org/wiki/Rubber_duck_debugging">rubber duck</a>. And it turned
out to be a great idea, as it gave me some good analogies.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2 id="toc_1.1">Dining Out</h2>

<p>
The analogies go like this: you walk into a burger join, and you're hungry.
</p>

<ul>
<li>
Dependency Injection is like ordering off the menu -- but specifying things
   like, "I'd like to substitute portabella mushrooms for the patties, please."
   The waiter then goes and brings your dish, which has portabella mushrooms
   instead of the hamburger patties listed on the menu.
</li>
<li>
Service Location is like ordering with substitutions, and having the waiter
   completely ignore the substitutions; you get what's on the menu, nothing
   more, nothing less.
</li>
</ul>

<p>
Now, when it comes to Zend Framework's version 1 releases, we've really got
neither. Our situation is more like a buffet or a kitchen -- you grab a little
of this, a little of that, and assemble your own burger. It's a lot more work.
</p>

<p>
Frankly, I'm lazy, and like my dinner brought to me... and if I want any
substitutions, I'd like those, too.
</p>

<h2 id="toc_1.2">Getting the Ingredients</h2>

<p>
A number of developers I've talked to seem to think DI is a bit too much 
"magic" -- they're worried they'll lose control over their application: they
won't know where dependencies are being set.
</p>

<p>
There are two things to keep in mind: 
</p>

<ol>
<li>
you, the developer, define the dependencies up front
</li>
<li>
if you don't pull the object from the container, you're in charge
</li>
</ol>

<p>
Regarding the second point, it appears some developers think that with a DI
container in place, dependencies magically get injected in <em>every</em> object. But
that's simply not the case. If you use normal PHP:
</p>

<div class="example"><pre><code class="language-php">
$o = new SomeClass();
</code></pre></div>

<p>
you'll get a new instance, just like always, configured only with any parameters
you pass in to the constructor or methods you call on it. It's only when you
retrieve the object from the DI container that you dependency injection takes
place; if you do that, you can always examine the DI configuration (which can
either be programmatic or via a configuration file) to determine what
dependencies were configured.
</p>

<p>
Basically, it's like the difference between making your own hamburger patty out
of fresh ground sirloin, and ordering Animal Style from In-N-Out.
</p>

<h2 id="toc_1.3">I'm done now</h2>

<p>
What's your favorite way of thinking of these concepts?
</p>
EOT;
$entry->setExtended($extended);

return $entry;
