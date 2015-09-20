---
id: 2014-05-08-angular-ui-router-reload
author: matthew
title: 'A Better $state.reload for the AngularJS UI-Router'
draft: false
public: true
created: '2014-05-08T12:00:00-05:00'
updated: '2014-05-08T12:00:00-05:00'
tags:
    - javascript
    - angularjs
    - ui-router
---
While working on [Apigility](http://apigility.org/), several times I ran into
an odd issue: after fetching new data via an API and assigning it to a scoped
variable, content would flash into existence… and then disappear. Nothing
would cause it to display again other than a browser reload of the page.

<!--- EXTENDED -->

Setup
-----

I have a page that lists a set of items. When you create an item, you push data
to the API, and, when done, the new item should be in that list.

First try: append to list
-------------------------

My first attempt was just appending the data to the list.

```javascript
service.create(data).then(function (newItem) {
    flash.success = 'Successfully created something';
    /* append new item to list */
    $scope.services.push(newItem);
});
```

This worked… until you left that screen and returned. At that point, the new
item would be gone, even if I coded my ui-router states to force a cache
refresh.

Refresh list
------------

My next attempt was to write a routine that would do a cache refresh after
creating the new item.

```javascript
service.create(data).then(function (newItem) {
    flash.success = 'Successfully created something';
    service.fetchAll(var force = true).then(function (services) {
        $scope.services = services;
    });
});
```

This is when I started noticing the "flash of content" problem. Essentially,
immediately after fetching the set of services, you'd see the new item
appended… and then it would disappear.

$state.reload()
---------------

At this point, I figured I'd use the ui-router to force a refresh, specifically
via `$state.reload()`.

```javascript
service.create(data).then(function (newItem) {
    flash.success = 'Successfully created something';
    service.fetchAll(var force = true).then(function (services) {
        $scope.services = services;
        $state.reload();
    });
});
```

I tried both with and without setting the scoped variable. Initially, I thought
it was working — but, as it turned out, I missed a case. I tested every single
time with at least one item already in the list — and this approach worked.
However, when I tried with the list not yet populated, failure once again.

Success: $timeout
-----------------

Surprisingly, the least intuitive solution ended up working: introducing a
delay.

```javascript
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
```

I have a few things to note about this. First, I moved the "reload" into its
own promise. This was done to ensure it doesn't block on the scope assignment.
Second, I introduce a `$timeout` call. This essentially gives the scope a
chance to populate before the reload triggers. Some examples I saw did a 1ms
timeout; I found in practice that this was not long enough; 100ms was long
enough, and did not introduce a noticeable delay in UI responsiveness. Finally,
you'll note this does not use `$state.reload()`. This is due to discovering
that part of my problem is a [known bug in `$state.reload()`](https://github.com/angular-ui/ui-router/issues/582),
whereby state "resolve" configuration is not honored.

I hope this approach helps others — I've found it to be robust and predictable.
