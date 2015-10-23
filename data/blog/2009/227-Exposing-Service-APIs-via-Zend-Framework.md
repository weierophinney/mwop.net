---
id: 227-Exposing-Service-APIs-via-Zend-Framework
author: matthew
title: 'Exposing Service APIs via Zend Framework'
draft: false
public: true
created: '2009-10-23T19:42:00-04:00'
updated: '2009-10-28T06:43:56-04:00'
tags:
    - php
    - 'zend framework'
---
The hubbub surrounding "Web 2.0" is around sharing data. In the early
iterations, the focus was on "mashups" — consuming existing public APIs in order
to mix and match data in unique ways. Now, more often than not, I'm hearing more
about *exposing* services for others to consume. Zend Framework makes this
latter trivially easy via its various server classes.

<!--- EXTENDED -->

All Zend Framework server classes follow PHP's
[SoapServer](http://php.net/soapserver) API. In a nutshell, you can basically do
the following with any server class:

```php
$server = new Zend_XmlRpc_Server();
$server->setClass('My_Awesome_Api');
echo $server->handle();
```

Each server protocol we support in this way —
[SOAP](http://framework.zend.com/manual/en/zend.soap.html),
[XML-RPC](http://framework.zend.com/manual/en/zend.xmlrpc.server.html),
[JSON-RPC](http://framework.zend.com/manual/en/zend.json.server.html), and
[AMF](http://framework.zend.com/manual/en/zend.amf.server.html) — has its own
little nuances, but the basics follow the above pattern.

Where should you do this, however? Many developers want to stick this in their
MVC application directly, in order to have pretty URLs. However, the framework
team typically recommends against this. When serving APIs, you want responses to
return as quickly as possible, and as the servers basically encapsulate the
Front Controller and MVC patterns in their design, there's no good reason to
duplicate processes and add processing overhead.

Additionally, there's often a need to version your APIs. As you add new features
or need to change method signatures, you'll need to introduce a new version of
the API for developers to consume.

One recommendation to solve each problem is to move your server endpoints into
your public directory structure, and then utilize your web server's URL
rewriting capabilities. As an example, you could organize your endpoints as
follows:

```
public
|-- api
|   |-- v1
|   |   |-- xmlrpc.php
|   |   |-- soap.php
|   |   |-- jsonrpc.php
```

You might then configure your URL rewriting as follows:

```apacheconf
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [NC,L]
RewriteRule ^api/v1/xmlrpc api/v1/xmlrpc.php [L]
RewriteRule ^api/v1/soap api/v1/soap.php [L]
RewriteRule ^api/v1/jsonrpc api/v1/jsonrpc.php [L]
RewriteRule ^.*$ index.php [NC,L]
```

This allows you to move the service scripts to other locations if necessary, as
well as to have each have explicit dependencies to insulate them from changes
elsewhere in the codebase.

As a standard best practice, you do not want code duplication. Code duplication
becomes quite common when taking the above strategy, as each endpoint script
will often have common logic for bootstrapping the application. One way you can
avoid this is to leverage
[Zend_Application](http://framework.zend.com/manual/en/zend.application.html).
You can do this in one of two ways: (1) instantiate `Zend_Application` using the
same configuration as your MVC application, and selectively bootstrap necessary
resources; or (2) extend your MVC application's bootstrap class, and override
the `run()` method.

In the first case, you might do the following in your server endpoint scripts:

```php
// Initialize application
require_once 'Zend/Application.php';
$app = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH .  '/configs/application.ini'
);

// Selectively bootstrap resources:
$app->bootstrap('db');

// Instantiate server, etc.
$server = new Zend_XmlRpc_Server();
```

In the second case, you would subclass your application bootstrap class, and
override the `run()` method. Such an extending class could look like the
following:

```php
class XmlRpc_Bootstrap extends Bootstrap
{
    public function run()
    {
        $server = new Zend_XmlRpc_Server();
        $server->setClass('My_Awesome_Api');
        echo $server->handle();
    }
}
```

You would also need to modify your application bootstrapping slightly to notify
it of your new bootstrap class:

```php
$app = new Zend_Application(
    APPLICATION_ENV,
    array(
        'bootstrap' => array(
            'class' => 'XmlRpc_Bootstrap',
            'path'  => 'path/to/Bootstrap.php',
        ),
        'config' => APPLICATION_PATH . '/configs/application.ini',
    ),
);
$app->bootstrap()
    ->run();
```

So, the takeaway is: Zend Framework makes exposing web services easy, and the
addition of `Zend_Application` makes it trivially easy to re-use application
configuration in order to expose your servers via discrete, unique endpoints in
your application. What are *you* waiting for?
