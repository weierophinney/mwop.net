<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-05-08-angular-ui-router-reload');
$entry->setTitle('A Better $state.reload for the AngularJS UI-Router');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-05-08 12:00', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2014-05-08 12:00', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'javascript',
  'angularjs',
  'ui-router',
));

$body =<<<'EOT'
<p>
    While working on <a href="http://apigility.org/">Apigility</a>, several times I ran into
    an odd issue: after fetching new data via an API and assigning it to a scoped variable,
    content would flash into existence... and then disappear. Nothing would cause it to display
    again other than a browser reload of the page.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Setup</h2>

<p>
    I have a page that lists a set of items. When you create an item, you push data to the API,
    and, when done, the new item should be in that list.
</p>

<h2>First try: append to list</h2>

<p>
    My first attempt was just appending the data to the list.
</p>

<div class="example"><pre><code class="language-javascript">
service.create(data).then(function (newItem) {
    flash.success = 'Successfully created something';
    /* append new item to list */
    $scope.services.push(newItem);
});
</code></pre></div>

<p>
    This worked... until you left that screen and returned. At that point, the 
    new item would be gone, even if I coded my ui-router states to force a cache
    refresh.
</p>

<h2>Refresh list</h2>

<p>
    My next attempt was to write a routine that would do a cache refresh after
    creating the new item.
</p>

<div class="example"><pre><code class="language-javascript">
service.create(data).then(function (newItem) {
    flash.success = 'Successfully created something';
    service.fetchAll(var force = true).then(function (services) {
        $scope.services = services;
    });
});
</code></pre></div>

<p>
    This is when I started noticing the "flash of content" problem. Essentially, immediately
    after fetching the set of services, you'd see the new item appended... and then it would
    disappear.
</p>

<h2>$state.reload()</h2>

<p>
    At this point, I figured I'd use the ui-router to force a refresh, specifically via
    <code>$state.reload()</code>.
</p>

<div class="example"><pre><code class="language-javascript">
service.create(data).then(function (newItem) {
    flash.success = 'Successfully created something';
    service.fetchAll(var force = true).then(function (services) {
        $scope.services = services;
        $state.reload();
    });
});
</code></pre></div>

<p>
    I tried both with and without setting the scoped variable. Initially, I thought it was
    working -- but, as it turned out, I missed a case. I tested every single time with at
    least one item already in the list -- and this approach worked. However, when I tried
    with the list not yet populated, failure once again.
</p>

<h2>Success: $timeout</h2>

<p>
    Surprisingly, the least intuitive solution ended up working: introducing a delay.
</p>

<div class="example"><pre><code class="language-javascript">
service.create(data).then(function (newItem) {
    flash.success = 'Successfully created something';
    service.fetchAll(var force = true)
        .then(function (services) {
            $scope.services = services;
        }).then(function () {
            return $timeout(function () {
                $state.go('.', {}, { reload: true });
            }, 100);
        });
});
</code></pre></div>

<p>
    I have a few things to note about this. First, I moved the "reload" into its own promise. This was done
    to ensure it doesn't block on the scope assignment. Second, I introduce a <code>$timeout</code>
    call. This essentially gives the scope a chance to populate before the reload triggers. Some
    examples I saw did a 1ms timeout; I found in practice that this was not long enough; 100ms was
    long enough, and did not introduce a noticeable delay in UI responsiveness. Finally, you'll
    note this does not use <code>$state.reload()</code>. This is due to discovering that part of my
    problem is a <a href="https://github.com/angular-ui/ui-router/issues/582">known bug in <code>$state.reload()</code></a>,
    whereby state "resolve" configuration is not honored.
</p>

<p>
    I hope this approach helps others -- I've found it to be robust and predictable.
</p>

EOT;
$entry->setExtended($extended);

return $entry;
