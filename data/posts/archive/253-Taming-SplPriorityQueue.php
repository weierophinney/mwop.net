<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('253-Taming-SplPriorityQueue');
$entry->setTitle('Taming SplPriorityQueue');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1295276520);
$entry->setUpdated(1295647645);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'spl',
));

$body =<<<'EOT'
<p>
<a href="http://php.net/SplPriorityQueue">SplPriorityQueue</a> is a fantastic new feature of
PHP 5.3. However, in trying to utilize it in a few projects recently, I've run
into some behavior that's (a) non-intuitive, and (b) in some cases at least,
undesired. In this post, I'll present my solutions.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2 id="toc_1.1">Of Heaps and Queues</h2>

<p>
<em>Queues</em> in programming are any data structure that, when iterated, return
values in a "first-in-first-out" (FIFO) order. For "last-in-first-out" (LIFO)
iteration, you define a <em>stack</em>.
</p>

<p>
A <em>heap</em> is a data structure where, given a specific node, all nodes beneath it
are of a value less than it.  (Technically, this would be considered a
"max-heap," as you can also have a variant where all child nodes are of a value
greater; this is called a "min-heap.")
</p>

<p>
A <em>priority queue</em> is a specialized version of a max-heap. Typically, data is
registered with a specific priority -- so the max-heap is looking at only the
priority value, not the data itself. This allows inserting data into the queue
in any order desired, while ensuring that they are iterated in the order
specified by the priorities provided.
</p>

<p>
PHP offers SPL data structures corresponding to each:
</p>

<ul>
<li>
<a href="http://php.net/SplQueue">SplQueue</a>, corresponding to a <em>queue</em>.
</li>
<li>
<a href="http://php.net/SplStack">SplStack</a>, corresponding to a <em>stack</em>.
</li>
<li>
<a href="http://php.net/SplHeap">SplHeap</a>, corresponding to a <em>heap</em>.
</li>
<li>
<a href="http://php.net/SplMaxHeap">SplMaxHeap</a>, correspondaxg to a <em>max-heap</em>.
</li>
<li>
<a href="http://php.net/SplMinHeap">SplMinHeap</a>, corresponding to a <em>min-heap</em>.
</li>
<li>
<a href="http://php.net/SplPriorityQueue">SplPriorityQueue</a>, corresponding to a
   <em>priority queue</em>.
</li>
</ul>

<h2 id="toc_1.2">Problems</h2>

<p>
The first problem I ran into was really a lapse of reasoning on my part, and is
namely this:
</p>
<blockquote>
<em>Iterating over a heap removes the values from the heap.</em>
</blockquote>
    
<p>
Basically, in order to satisfy the <em>heap</em> contract, which is that the root node
is always the maximum value (or minimum, in the case of a min-heap), any
previous nodes must be removed.
</p>

<p>
The problem with this, obviously, is that if you want to iterate over a heap of
any sort multiple times, well, you can't with the same instance.
</p>

<p>
The next problem I ran into was with SplPriorityQueue specifically: when items
of equal priority are enqueued, the iteration order of these items is...
unexpected. While the <a href="http://php.net/splpriorityqueue.compare">documentation</a>
notes that "multiple elements with the same priority will get dequeued in no
particular order," the fact is that it <em>is</em> predictable, and unintuitive. For
example, given the following:
</p>

<div class="example"><pre><code class="language-php">
$queue-&gt;insert('foo', 1000);
$queue-&gt;insert('bar', 1000);
$queue-&gt;insert('baz', 1000);
$queue-&gt;insert('bat', 1000);

foreach ($queue as $data) echo $data, \&quot; \&quot;;
</code></pre></div>

<p>
I'd expect a result of "foo bar baz bat", assuming FIFO order (which is expected
in a <em>queue</em>) for equal priorities; "foo baz bat bar", assuming ordering by
data (which might be expected in a max-heap). In fact, neither is true: the
first item will be emitted first, and then the remaining items in reverse order
of when enqueued: "foo bat baz bar".
</p>

<p>
While this may be somewhat predictable, I find I don't want to assume such
order, nor try and write code around it.
</p>

<h2 id="toc_1.3">Solutions</h2>

<h3 id="toc_1.3.1">Allowing multiple iterations</h3>

<p>
Allowing multiple iterations of a queue is as easy as cloning it prior to
iteration:
</p>

<div class="example"><pre><code class="language-php">
foreach (clone $queue as $datum) echo $datum, \&quot; \&quot;;
</code></pre></div>

<p>
The problem is automating this -- there are cases where I don't want users to
really have to understand the internal implementation.
</p>

<p>
My solution to this was to use the idea of inner and outer iterators. In this
particular case, I created a "PriorityQueue" class that composes an
SplPriorityQueue instance, and which also implements <code>IteratorAggregate</code>. This
allows the following:
</p>

<div class="example"><pre><code class="language-php">
namespace Foo;

class PriorityQueue implements Countable, IteratorAggregate
{
    protected $innerQueue;
    
    public function __construct()
    {
        // I'll explain the lack of global namespacing later...
        $this-&gt;innerQueue = new SplPriorityQueue;
    }

    public function count()
    {
        return count($this-&gt;innerQueue);
    }

    public function insert($datum, $priority)
    {
        $this-&gt;innerQueue-&gt;insert($datum, $priority);
    }
    
    public function getIterator()
    {
        return clone $this-&gt;innerQueue;
    }
}
</code></pre></div>

<p>
This approach means that as I consume PriorityQueue, I can be assured that I
can count and iterate over it... again and again.
</p>

<p>
I mention in the code comments that I'm not importing SplPriorityQueue into the
namespace. The reason is that I want to also solve the problem of predictable
queue order.
</p>

<h3 id="toc_1.3.2">Enforcing predictable queue order</h3>

<p>
The solution to the queue order problem with equal priorities is actually quite simple. While I found it on <a href="http://php.net/splpriorityqueue.compare">the SplPriorityQueue::compare manual page</a>, <a href="http://twitter.com/elazar">Matthew Turland</a> also <a href="http://www.slideshare.net/tobias382/new-spl-features-in-php-53">discusses it in a presentation on SPL</a>, and it hinges on one, simple fact: <em>priorities do not need to be integers</em>.
</p>

<p>
What does this mean? It means that the following are not equivalent, and will
lead to a more expected sort order:
</p>

<div class="example"><pre><code class="language-php">
$queue-&gt;insert('foo', array(1000, 1000));
$queue-&gt;insert('bar', array(1000, 100));
$queue-&gt;insert('baz', array(1000, 10));
$queue-&gt;insert('bat', array(1000, 1));

foreach ($queue as $data) echo $data, \&quot; \&quot;;
</code></pre></div>

<p>
This results in "foo bar baz bat"!
</p>

<p>
The trick, then, is automating the solution. I achieved this in a custom
SplPriorityQueue extension:
</p>

<div class="example"><pre><code class="language-php">
namespace Foo;

class SplPriorityQueue extends \SplPriorityQueue
{
    protected $queueOrder = PHP_INT_MAX;

    public function insert($datum, $priority)
    {
        if (is_int($priority)) {
            $priority = array($priority, $this-&gt;queueOrder--);
        }
        parent::insert($datum, $priority);
    }
}
</code></pre></div>

<p>
As each datum is added to the queue, if the priority is an integer, it wraps it
in an array, using <code>$queueOrder</code> as a second value to the array, and
decrementing <code>$queueOrder</code> on completion. The new priority is then used to
insert the value.
</p>

<p>
Using this extension ensures that order in the priority queue is now
predictable.
</p>

<h2 id="toc_1.4">Conclusions</h2>

<p>
SplPriorityQueue is indeed powerful, and saves me a ton of time programming --
and also likely CPU processes and memory when using larger data sets. While it
may not always meet my use cases, the fact is that, particularly with
namespacing available, I can easily override the class to meet my needs.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
