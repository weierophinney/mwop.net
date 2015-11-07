---
id: 253-Taming-SplPriorityQueue
author: matthew
title: 'Taming SplPriorityQueue'
draft: false
public: true
created: '2011-01-17T10:02:00-05:00'
updated: '2011-01-21T17:07:25-05:00'
tags:
    - php
    - spl
---
[SplPriorityQueue](http://php.net/SplPriorityQueue) is a fantastic new feature
of PHP 5.3. However, in trying to utilize it in a few projects recently, I've
run into some behavior that's (a) non-intuitive, and (b) in some cases at
least, undesired. In this post, I'll present my solutions.

<!--- EXTENDED -->

Of Heaps and Queues
-------------------

*Queues* in programming are any data structure that, when iterated, return
values in a "first-in-first-out" (FIFO) order. For "last-in-first-out" (LIFO)
iteration, you define a *stack*.

A *heap* is a data structure where, given a specific node, all nodes beneath it
are of a value less than it. (Technically, this would be considered a
"max-heap," as you can also have a variant where all child nodes are of a value
greater; this is called a "min-heap.")

A *priority queue* is a specialized version of a max-heap. Typically, data is
registered with a specific priority — so the max-heap is looking at only the
priority value, not the data itself. This allows inserting data into the queue
in any order desired, while ensuring that they are iterated in the order
specified by the priorities provided.

PHP offers SPL data structures corresponding to each:

- [SplQueue](http://php.net/SplQueue), corresponding to a *queue*.
- [SplStack](http://php.net/SplStack), corresponding to a *stack*.
- [SplHeap](http://php.net/SplHeap), corresponding to a *heap*.
- [SplMaxHeap](http://php.net/SplMaxHeap), correspondaxg to a *max-heap*.
- [SplMinHeap](http://php.net/SplMinHeap), corresponding to a *min-heap*.
- [SplPriorityQueue](http://php.net/SplPriorityQueue), corresponding to a *priority queue*.

Problems
--------

The first problem I ran into was really a lapse of reasoning on my part, and is
namely this:

> *Iterating over a heap removes the values from the heap.*

Basically, in order to satisfy the *heap* contract, which is that the root node
is always the maximum value (or minimum, in the case of a min-heap), any
previous nodes must be removed.

The problem with this, obviously, is that if you want to iterate over a heap of
any sort multiple times, well, you can't with the same instance.

The next problem I ran into was with SplPriorityQueue specifically: when items
of equal priority are enqueued, the iteration order of these items is…
unexpected. While the [documentation](http://php.net/splpriorityqueue.compare)
notes that "multiple elements with the same priority will get dequeued in no
particular order," the fact is that it *is* predictable, and unintuitive. For
example, given the following:

```php
$queue->insert('foo', 1000);
$queue->insert('bar', 1000);
$queue->insert('baz', 1000);
$queue->insert('bat', 1000);

foreach ($queue as $data) echo $data, " ";
```

I'd expect a result of "foo bar baz bat", assuming FIFO order (which is
expected in a *queue*) for equal priorities; "foo baz bat bar", assuming
ordering by data (which might be expected in a max-heap). In fact, neither is
true: the first item will be emitted first, and then the remaining items in
reverse order of when enqueued: "foo bat baz bar".

While this may be somewhat predictable, I find I don't want to assume such
order, nor try and write code around it.

Solutions
---------

### Allowing multiple iterations

Allowing multiple iterations of a queue is as easy as cloning it prior to
iteration:

```php
foreach (clone $queue as $datum) echo $datum, " ";
```

The problem is automating this — there are cases where I don't want users to
really have to understand the internal implementation.

My solution to this was to use the idea of inner and outer iterators. In this
particular case, I created a `PriorityQueue` class that composes an
`SplPriorityQueue` instance, and which also implements `IteratorAggregate`. This
allows the following:

```php
namespace Foo;

class PriorityQueue implements Countable, IteratorAggregate
{
    protected $innerQueue;
    
    public function __construct()
    {
        // I'll explain the lack of global namespacing later...
        $this->innerQueue = new SplPriorityQueue;
    }

    public function count()
    {
        return count($this->innerQueue);
    }

    public function insert($datum, $priority)
    {
        $this->innerQueue->insert($datum, $priority);
    }
    
    public function getIterator()
    {
        return clone $this->innerQueue;
    }
}
```

This approach means that as I consume `PriorityQueue`, I can be assured that I
can count and iterate over it… again and again.

I mention in the code comments that I'm not importing `SplPriorityQueue` into
the namespace. The reason is that I want to also solve the problem of
predictable queue order.

### Enforcing predictable queue order

The solution to the queue order problem with equal priorities is actually quite
simple. While I found it on [the SplPriorityQueue::compare manual page](http://php.net/splpriorityqueue.compare),
[Matthew Turland](http://twitter.com/elazar) also
[discusses it in a presentation on SPL](http://www.slideshare.net/tobias382/new-spl-features-in-php-53),
and it hinges on one, simple fact: *priorities do not need to be integers*.

What does this mean? It means that the following are not equivalent, and will
lead to a more expected sort order:

```php
$queue->insert('foo', array(1000, 1000));
$queue->insert('bar', array(1000, 100));
$queue->insert('baz', array(1000, 10));
$queue->insert('bat', array(1000, 1));

foreach ($queue as $data) echo $data, " ";
```

This results in "foo bar baz bat"!

The trick, then, is automating the solution. I achieved this in a custom
`SplPriorityQueue` extension:

```php
namespace Foo;

class SplPriorityQueue extends \SplPriorityQueue
{
    protected $queueOrder = PHP_INT_MAX;

    public function insert($datum, $priority)
    {
        if (is_int($priority)) {
            $priority = array($priority, $this->queueOrder--);
        }
        parent::insert($datum, $priority);
    }
}
```

As each datum is added to the queue, if the priority is an integer, it wraps it
in an array, using `$queueOrder` as a second value to the array, and
decrementing `$queueOrder` on completion. The new priority is then used to
insert the value.

Using this extension ensures that order in the priority queue is now
predictable.

Conclusions
-----------

`SplPriorityQueue` is indeed powerful, and saves me a ton of time programming —
and also likely CPU processes and memory when using larger data sets. While it
may not always meet my use cases, the fact is that, particularly with
namespacing available, I can easily override the class to meet my needs.
