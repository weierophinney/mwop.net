---
id: 2014-03-26-apigility-rpc-with-hal
author: matthew
title: 'Apigility: Using RPC with HAL'
draft: false
public: true
created: '2014-03-26T15:30:00-05:00'
updated: '2014-03-26T15:30:00-05:00'
tags:
    - php
    - apigility
    - zf2
    - 'zend framework'
    - rest
    - hal
---
A few days ago, we [released our first beta of Apigility](http://bit.ly/ag-1-beta1).
We've started our documentation effort now, and one question has arisen a few
times that I want to address: How can you use Hypermedia Application Language
(HAL) in RPC services?

<!--- EXTENDED -->

HAL?
----

[Hypermedia Application Language](http://tools.ietf.org/html/draft-kelly-json-hal-06)
is an IETF proposal for how to represent resources and their relations within
APIs.  Technically, it provides two mediatypes, `application/hal+json` and
`application/hal+xml`; however, Apigility only provides the JSON variant.

The important things to know about HAL are:

- It provides a standard way of describing relational links. All relational
  links are under a `_links` property of the resource. That property is an
  object. Each property of that object is a link relation; the value of each
  link relation is an object (or array of such objects) describing the link
  that must minimally contain an `href` proerty. The link object itself can
  contain some additional metadata, such as a mediatype, a name (useful for
  differentiating between multiple link objects assigned to the same relation).

  While not required, the specification recommends resources contain a "self"
  relational link, indicating the canonical location for the resource. This is
  particularly useful when we consider embedding (the next topic).

  Sound hard? It's not:

  ```javascript
  {
      "_links": {
          "self": {
              "href": "/blog/2014-03-26-apigility-rpc-with-hal"
          }
      }
  }
  ```

- Besides link relations, HAL also provides a standard way of describing
  *embedded resources*. An embedded resource is any other resource you can
  address via your API, and, as such, would be structured as a HAL resource —
  in other words, it would have a `_links` property with relational links.
  Essentially, any property of the resource you're returning that can itself be
  addressed via the URI must be *embedded* in the resource. This is done via
  the property `_embedded`.

  Like `_links`, `_embedded` is an object. Each key in the object is the local
  name by which the resource refers to the embedded resource. The value of such
  keys can either be HAL resources or *arrays* of HAL resources; in fact, this
  is how *collections* are represented in HAL!

  As examples:

  ```javascript
  {
      "_links": {
          "self": {
              "href": "/blog/2014-03-26-apigility-rpc-with-hal"
          }
      },
      "_embedded": {
          "author": {
              "_links": {
                  "self": {
                      "href": "/blog/author/matthew"
                  }
              },
              "id": "matthew",
              "name": "Matthew Weier O'Phinney",
              "url": "http://mwop.net"
          },
          "tags": [
              {
                  "_links": {
                      "self": {
                          "href": "/blog/tag/php"
                      }
                  },
                  "id": "php"
              },
              {
                  "_links": {
                      "self": {
                          "href": "/blog/tag/rest"
                      }
                  },
                  "id": "rest"
              }
          ]
      }
  }
  ```
                
  The example above shows two embedded resources. The first is the author; the
  second, a collection of tags. Note that *every* object under `_embedded` is a
  HAL object!

  You can go quite far with this — you can also have embedded resources inside
  your embedded resources, arbitrarily deep.

RPC?
----

RPC stands for Remote Procedure Call, and, when describing a web API, is
usually used to describe a web service that publishes multiple method calls at
a single URI using only `POST`; XML-RPC and SOAP are the usual suspects.

In Apigility, we use the term RPC in a much looser sense; we use it to describe
one-off services: actions like "authenticate," or "notify," or "register" would
all make sense here. They are actions that usually only need to respond to a
single HTTP method, and which may or may not describe a "thing", which is what
we usually consider a "resource" when discussing REST terminology.

That said: what if what we want to return from the RPC call *are* REST
resources?

Returning HAL from RPC Services
-------------------------------

In order to return HAL from RPC services, we need to understand (a) how Content
Negotiation works, and (b) what needs to be returned in order for the HAL
renderer to be able to create a representation.

For purposes of this example, I'm positing a `RegisterController` as an RPC
service that, on success, is returning a `User` object that I want rendered as
a HAL resource.

The [zf-content-negotiation](https://github.com/zfcampus/zf-content-negotiation)
module takes care of content negotiation for Apigility. It introspects the
`Accept` header in order to determine if we can return a representation, and
then, if it can, will cast any `ZF\ContentNegotiation\ViewModel` returned from
a controller to the appropriate view model for the representation. From there,
a renderer will pick up the view model and do what needs to be done.

So, the first thing we have to do is return `ZF\ContentNegotiation\ViewModel`
instances from our controller.

```php
use Zend\Mvc\Controller\AbstractActionController;
use ZF\ContentNegotiation\ViewModel;

class RegisterController extends AbstractActionController
{
    public function registerAction()
    {
        /* ... do some work ... get a user ... */
        return new ViewModel(array('user' => $user));
    }
}
```

The [zf-hal](https://github.com/zfcampus/zf-hal) module in Apigility creates
the actual HAL representations. `zf-hal` looks for a "payload" variable in the
view model, and expects that value to be either a `ZF\Hal\Entity` (single item)
or `ZF\Hal\Collection`. When creating an `Entity` object, you need the object
being represented, as well as the identifier. So, let's update our return
value.

```php
use Zend\Mvc\Controller\AbstractActionController;
use ZF\ContentNegotiation\ViewModel;
use ZF\Hal\Entity;

class RegisterController extends AbstractActionController
{
    public function registerAction()
    {
        /* ... do some work
         * ... get a $user
         * ... assume we have also now have an $id
         */
        return new ViewModel(array('payload' => array(
            'user' => new Entity($user, $id),
        )));
    }
}
```

`zf-hal` contains what's called a "metadata map". This is a map of classes to
information on how `zf-hal` should render them: what route to use, what
additional relational links to inject, how to serialize the object, what field
represents the identifier, etc.

In most cases, you will have likely already defined a REST service for the
resource you want to return from the RPC service, in which case you will be
done. However, if you want, you can go in and manually configure the metadata
map in your API module's `config/module.config.php` file:

```php
return array(
    /* ... */
    'zf-hal' => array(
        'metadata_map' => array(
            'User' => array(
                'route_name' => 'api.rest.user',
                'entity_identifier_name' => 'username',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\Stdlib\Hydrator\ObjectProperty',
            ),
        ),
    ),
);
```

Finally, we need to make sure that the service is configured to actually return
HAL. We can do this in the admin if we want. Find the "Content Negotiation"
section of the admin, and the "Content Negotiation Selector" item, and set that
to "HalJson"; don't forget to save! Alternately, you can do this manually in
the API module's `config/module.config.php` file, under the
`zf-content-negotiation` section:

```php
return array(
    /* ... */
    'zf-content-negotiation' => array(
        'controllers' => array(
            /* ... */
            'RegisterController' => 'HalJson',
        ),
        /* ... */
    ),
);
```

Once your changes are complete, when you make a successful request to the URI
for your "register" RPC service, you'll receive a HAL response pointing to the
canonical URI for the user resource created!
