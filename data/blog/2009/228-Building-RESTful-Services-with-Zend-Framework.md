---
id: 228-Building-RESTful-Services-with-Zend-Framework
author: matthew
title: 'Building RESTful Services with Zend Framework'
draft: false
public: true
created: '2009-11-09T09:00:00-05:00'
updated: '2009-11-11T10:38:41-05:00'
tags:
    - php
    - rest
    - 'zend framework'
---
As a followup to my [previous post](/blog/227-Exposing-Service-APIs-via-Zend-Framework.html),
I now turn to RESTful web services. I originally encountered the term when attending
php|tropics in 2005, where [George Schlossnaggle](http://twitter.com/g_schlossnagle)
likened it to simple GET and POST requests. Since then, the architectural style
— and developer understanding of the architectural style — has improved a bit,
and a more solid definition can be made.

<!--- EXTENDED -->

At its heart,
[REST](http://en.wikipedia.org/wiki/Representational_State_Transfer) simply
dictates that a given resource have a unique address, and that you interact with
that resource using HTTP verbs. The standard verbs utilized are:

- *GET*: retrieve a list of resources, or, if an identifier is present, view a single resource
- *POST*: create a new resource with the data provided in the POST
- *PUT*: update an existing resource as specified by an identifier, using the PUT data
- *DELETE*: delete an existing resource as specified by an identifier

The standard URL structure used is as follows:

- `/resource` - GET (list) and POST operations
- `/resource/{identifier}` - GET (view), PUT, and DELETE operations

What the REST paradigm provides you is a simple, standard way to structure your
CRUD (Create-Read-Update-Delete) applications. Due to the large number of REST
clients available, it also means that if you follow the rules, you get a ton of
interoperability with those clients.

As of [Zend Framework](http://framework.zend.com/) 1.9.0, it's trivially easy to
create RESTful routes for your MVC application, as well as to handle the various
REST actions via action controllers.

[Zend_Rest_Route](http://framework.zend.com/manual/en/zend.controller.router.html#zend.controller.router.routes.rest)
allows you to define RESTful controllers at several levels:

- You can make it the default route, meaning that unless you have additional
  routes, all controllers will be considered REST controllers.
- You can specify modules that contain RESTful controllers.
- You can specify specific controllers per module that are RESTful

As examples:

```php
$front = Zend_Controller_Front::getInstance();
$router = $front->getRouter();

// Specifying all controllers as RESTful:
$restRoute = new Zend_Rest_Route($front);
$router->addRoute('default', $restRoute);

// Specifying the "api" module only as RESTful:
$restRoute = new Zend_Rest_Route($front, array(), array(
    'api',
));
$router->addRoute('rest', $restRoute);

// Specifying the "api" module as RESTful, and the "task" controller of the
// "backlog" module as RESTful:
$restRoute = new Zend_Rest_Route($front, array(), array(
    'api',
    'backlog' => array('task'),
));
$router->addRoute('rest', $restRoute);
```

To define a RESTful action controller, you can either extend
`Zend_Rest_Controller`, or simply define the following methods in a standard
controller extending `Zend_Controller_Action` (you'll need to define them
regardless):

```php
// Or extend Zend_Rest_Controller
class RestController extends Zend_Controller_Action
{
    // Handle GET and return a list of resources
    public function indexAction() {}

    // Handle GET and return a specific resource item
    public function getAction() {}

    // Handle POST requests to create a new resource item
    public function postAction() {}

    // Handle PUT requests to update a specific resource item
    public function putAction() {}

    // Handle DELETE requests to delete a specific item
    public function deleteAction() {}
}
```

For those methods that operate on individual resources (`getAction()`,
`putAction()`, and `deleteAction()`), you can test for the identifier using the
following:

```php
if (!$id = $this->_getParam('id', false)) {
    // report error, redirect, etc.
}
```

Responding is an art
--------------------

Many developers are either unaware of or ignore the part of the specification
that dictates what the *response* should look like.

For instance, in classic REST, after performing a POST to create a new item, you
should do the following:

- Set the HTTP response code to 201, indicating "Created"
- Set the Location header to point to the canonical URI for the newly created item: `/team/31`
- Provide a representation of the newly created item

Note that there's no redirect, which flies in the face of standard web
development (where GET-POST-Redirect is the typical format). This is a common
"gotcha" moment.

Similarly, with PUT requests, you simply indicate an HTTP 200 status when
successful, and show a representation of the updated item. DELETE requests
should return an HTTP 204 status (indicating success - no content), with no body
content.

*Note: when building RESTful HTML applications, you may want to still do GET-POST-Redirect to prevent caching issues. The above applies to RESTful web services, which typically use XML or JSON for transactions, and have smart clients for interacting with the service.*

I'll be writing another article soon showing some tips and tricks for
interacting with HTTP headers, both from the request and for the response, as
it's a subject lengthy enough for a post of its own. In the meantime, start
playing with `Zend_Rest_Route` and standardizing on it for your CRUD operations!
