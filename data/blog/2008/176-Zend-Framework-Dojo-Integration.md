---
id: 176-Zend-Framework-Dojo-Integration
author: matthew
title: 'Zend Framework Dojo Integration'
draft: false
public: true
created: '2008-05-21T10:57:00-04:00'
updated: '2008-05-25T12:40:37-04:00'
tags:
    0: php
    1: mvc
    3: 'zend framework'
---
I'm pleased to announce that [Zend Framework](http://framework.zend.com/) will
be partnering with [Dojo Toolkit](http://dojotoolkit.org/) to deliver
out-of-the-box Ajax and rich user interfaces for sites developed in Zend
Framework.

First off, for those ZF users who are using other Javascript toolkits: Zend
Framework will continue to be basically JS toolkit agnostic. You will still be
able to use whatever toolkit you want with ZF applications. ZF will simply be
shipping Dojo so that users have a toolkit by default. Several points of
integration have been defined, and my hope is that these can be used as a
blueprint for community contributions relating to other javascript frameworks.
In the meantime, developers choosing to use Dojo will have a rich set of
components and integration points to work with.

The integration points we have defined for our initial release are as follows:

<!--- EXTENDED -->

- **JSON-RPC Server:** We are re-working the `Zend_Json_Server` that has been
  in our incubator since, oh, what? 0.2.0? and never released to actually
  follow a specification: [JSON-RPC](http://groups.google.com/group/json-rpc).
  This will allow it to work seamlessly with Dojo, as well as other toolkits
  that have JSON-RPC client implementations. I have actually completed work on
  this, though the proposal is waiting to be approved; if you want to check it
  out, you can find it in the [ZF svn](http://framework.zend.com/svn/framework/branch/user/matthew/zed_json_server).

  The original `Zend_Json_Server` implementation will be abandoned. It was
  never fully tested nor fully documented, which has prevented its release.
  Additionally, since it implemented its own ad-hoc standard, it did not
  provide the type of interoperability that a true JSON-RPC server
  implementation will provide. I am excited that we will finally be able to
  provide a standards-compliant solution for general availability.

  One final note: there are currently two different JSON-RPC specifications,
  1.0 and 2.0. Currently, the implementation I've been working on will switch
  payload formats based on the request, and can deliver different SMD formats
  appropriately as well.

- **dojo() View Helper:** Enabling Dojo for a page is not typically as trivial
  as just loading the `dojo.js` script — you have a choice of loading it from
  the AOL CDN or a local path, and also may want or need to load additional
  dojo, dijit, or dojox modules, specify custom modules and paths, specify code
  to run at `onLoad()`, and specify stylesheets for decorating dijits. On top
  of this, this information may change from page to page, and may only be
  needed for a subset of pages. The `dojo()` view helper will act as a
  [placeholder](http://framework.zend.com/manual/en/zend.view.helpers.html#zend.view.helpers.initial.placeholder)
  implementation, and facilitate all of the above tasks, as well as take care
  of rendering the necessary `style` and `script` elements in your page.
- **Form Element implementations:** One area that developers really leverage
  javascript and ajax toolkits is forms. In particular, many types of form
  input can benefit from advanced and rich user interfaces that only javascript
  can provide: calendar choosers, time selectors, etc. Additionally, many like
  to use client-side validation in order to provide instantaneous validation
  feedback to users (instead of requiring a round-trip to the server). We will
  be identifying a small group of form elements that we feel solve the most
  relevant use cases, and write Dojo-specific versions that can be utilized
  with `Zend_Form`. (One thing to note: `Zend_Form`'s design already works very
  well with Dojo, allowing many widgets and client-side validations to be
  created by simply setting the appropriate element attributes.)
- **dojo.data Compatibility:** `dojo.data` defines a standard storage
  interface; services providing data in this format can then be consumed by a
  variety of Dojo facilities to provide highly flexible and dynamic content for
  your user interfaces. We will be building a component that will create
  `dojo.data` compatible payloads with which to respond to `XmlHttpRequest`s; you
  will simply need to pass in the data, and provide metadata regarding it.

So, some examples are in order. First off, `Zend_Json_Server` operates like all
of ZF's server components: if follows the [SoapServer API](http://php.net/soap_soapserver_construct).
This allows you to attach arbitrary classes and functions to the server
component. Additionally, it can build a Service Mapping Description (SMD) that
Dojo can consume in order to discover valid methods and signatures. As an
example, on the server side you could have the following:

```php
// /json-rpc.php
// Assumes you have a class 'Foo' with methods 'bar' and 'baz':
$server = new Zend_Json_Server();
$server->setClass('Foo')
       ->setTarget('/json-rpc.php')
       ->setEnvelope('JSON-RPC-1.0')
       ->setDojoCompatible(true);

// For GET requests, simply return the service map
if ('GET' == $_SERVER['REQUEST_METHOD']) {
    $smd = $server->getServiceMap();
    header('Content-Type: application/json');
    echo $smd;
    exit;
}

$server->handle();
```

On your view script side, you might then do the following:

```php
<h2>Dojo JSON-RPC Demo</h2>
<input name=\"foo\" type=\"button\" value=\"Demo\" onClick=\"demoRpc()\"/>
<? 
$this->dojo()->setLocalPath('/js/dojo/dojo.js')
             ->addStyleSheetModule('dijit.themes.tundra')
             ->requireModule('dojo.rpc.JsonService');
$this->headScript()->captureStart(); ?>
function demoRpc()
{
    var myObject = new dojo.rpc.JsonService('/json-rpc.php');
    console.log(myObject.bar());
}
<? $this->headScript()->captureEnd() ?>
```

And, finally, in your layout script, you might have the following:

```php
<?= $this->doctype() ?>
<html>
    <head>
        <title>...</title>
        <?= $this->dojo() ?>
        <?= $this->headScript() ?>
    </head>
    <body class=\"tundra\">
        <?= $this->layout()->content ?>
    </body>
</html>
```

The example doesn't do much — it simply logs the results of the JSON-RPC call
to the console — but it demonstrates a number of things:

- **dojo() View Helper:** The example shows using dojo from a local path
  relative to the server's document root; using the 'Tundra' stylesheet shipped
  with Dojo and attaching it to the layout; capturing a required module
  (`dojo.rpc.JsonService`); and rendering the necessary Dojo stylsheet and
  script includes in the layout.
- **JSON-RPC client:** Dojo requires that you point the JsonService to an
  endpoint that delivers a Service Mapping Description; in this example, I use
  any GET request to return the SMD. Once the SMD is retrieved, any methods
  exposed are available to the Javascript object as if they were internal
  methods — hence `myObject.bar()`. Dojo's current implementation performs all
  other requests as POST requests, passing the data via the raw POST body.

There will be more to come in the future, and I will be blogging about
developments as I get more proposals up and code into the repository. All in
all, this is a very exciting collaboration, and should help provide ZF
developers the ability to rapidly create web applications with rich, dynamic
user interfaces.

**Update:** [Andi has posted an FAQ on our integration](http://andigutmans.blogspot.com/2008/05/dojo-and-zend-framework-partnership.html).
