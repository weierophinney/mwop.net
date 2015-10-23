---
id: zf2-beta3
author: matthew
title: 'View Layers, Database Abstraction, Configuration, Oh, My!'
draft: false
public: true
created: '2012-03-05T17:32:45-05:00'
updated: '2012-03-05T17:32:45-05:00'
tags:
    - php
    - 'zend framework'
    - zf2
---
Late last week, the Zend Framework community
[released 2.0.0beta3](http://framework.zend.com/zf2/blog/entry/Zend-Framework-2-0-0beta3-Released),
the latest iteration of the v2 framework. What have we been busy doing the last
couple months? In a nutshell, getting dirty with view layers, database
abstraction, and configuration.

<!--- EXTENDED -->

View Layers
-----------

Working with and on Zend Framework as long as I have, one thing that has always
been a bit of a thorn in my side is how difficult it is to specify differing
view strategies based on arbitrary criteria. For instance, if I want to return
JSON, or XML, how can I do so? The "answer" in ZF1 is to use the `ContextSwitch`
action helper, which basically simply overloads the filename suffix used for
the view to include the format type — `.js.phtml`, `.xml.phtml`. This works,
but it's a bit of a hack. (Full disclosure: I authored the hack.)

Another problem I've always had is that rendering has occurred in multiple
places of request execution. Action view scripts are rendered immediately
following the action's execution, layouts are rendered at another time
entirely. Using `Zend_View` as the renderer, this is fine, but if you want to
switch to a solution that is capable of rendering the entire payload, including
"child" views, at once, it becomes quite difficult to work around.

For Zend Framework 2, I [proposed a new view layer](http://framework.zend.com/wiki/display/ZFDEV2/RFC+-+View+Layer),
which helps mitigate some of these factors. As part of the work for this
proposal, I reorganized the component slightly to separate it into "helpers",
"renderers", and "resolvers" — the latter are used to resolve a template to a
renderer-specific resource. Additionally, I introduced a new concept into the
framework, that of "View Models". This is a concept borrowed from a number of
different places, but most notably Microsoft, who implemented them as part of a
new pattern entitled "Model-View-ViewModel". In this pattern, you bind data to
a "ViewModel" object, which can contain optional presentation logic, and pass
the ViewModel to the View. The View then grabs data from the ViewModel to
present to the client.

The biggest change, however, was introducing a "gateway" class,
`Zend\View\View`, with the responsibility of martialling a renderer and
injecting a response object. The code for this leverages the `EventManager`
component to allow attaching "strategies" for selecting a renderer. The
selected renderer is then used to render the template present in the `ViewModel`
passed to the View object. Once complete, we loop through response strategies,
where the results of rendering can be injected into the response. This also
provides an ideal location for adding headers, such as HTTP caching headers.

Finally, I introduced a number of MVC listeners for view integration. Some of
these are quite basic — such as ensuring we have a listener that will trigger
the view, and a default rendering strategy. Others help streamline the MVC -
while we recommend returning ViewModel objects from your controllers, with
templates set, default listeners provided will create ViewModels from returned
associative arrays, and inject a template name based on the requested
controller and action.

What do you really need to know from the outset? Not much!

```php
namespace Sample\Controller;

use Zend\Mvc\Controller\ActionController;

class HelloController extends ActionController
{
    public function worldAction()
    {
        // Implicitly creates a ViewModel with the below variables, and sets
        // the template to hello/world (:controller/:action)
        return array(
            'message' => 'Hello world!',
        );
    }
}
```

Basically, the most common use case is the one you'd expect to work. The fun
really starts when you want to perform other common tasks: change the layout,
disable the layout, specify an alternate template, add additional templates to
render and aggregate in the layout, and more. In all cases, you work with
ViewModels, and then let the renderer worry about how to represent them.

For more details, [read the Zend\View quickstart](http://packages.zendframework.com/docs/latest/manual/en/zend.view.html#zend.view.quick-start).

Database Abstraction
--------------------

`Zend_Db` provides a ton of capabilities in Zend Framework v1. However, over
the years, we've discovered some design issues both in the code itself as well
as the tests which have made a number of features difficult to support, and
others difficult if not impossible to implement. As
[Ralph](http://ralphschindler.com/) noted in his
[DB refactoring RFC](http://framework.zend.com/wiki/display/ZFDEV2/RFC+-+Zend+Db),

> Each new feature request generally comes with it's own concerns that apply to
> the project as a whole: "How useful is the feature?", "How does this feature
> impact performance?", "How wide spread is the need for this feature?".
> Generally, features are added to the core component bloating the core
> component and adding a new set of code that has to be maintained.

The iteration for beta3 was to get the basic structure up and running for
drivers and adapters (drivers are the low-level connections, adapters provide
basic abstraction around common operations), resultset abstraction, the basic
infrastructure for SQL abstraction, metadata support, and a table/row data
gateway implementation. All operations were tested on PDO_Sqlite, mysqli, and
sqlsrv; preliminary reports indicate most PDO drivers work out of the box at
this point.

What does it look like?

### Inserting data

```php
// where $adapter is an adapter object
$qi = function($name) use ($adapter) { 
    return $adapter->platform->quoteIdentifier($name); 
};
$fp = function($name) use ($adapter) { 
    return $adapter->driver->formatParameterName($name); 
};

$sql = 'INSERT INTO '
    . $qi('artist')
    . ' (' . $qi('name') . ', ' . $qi('history') . ') VALUES ('
    . $fp('name') . ', ' . $fp('history') . ')';

$statement = $adapter->query($sql);

$parameters = array(
    'name'    => 'New Artist',
    'history' => 'This is the history',
);

$statement->execute($parameters);
```

### Selecting data

```php
// where $adapter is an adapter object
$qi = function($name) use ($adapter) { 
    return $adapter->platform->quoteIdentifier($name); 
};
$fp = function($name) use ($adapter) { 
    return $adapter->driver->formatParameterName($name); 
};

$sql = 'SELECT * FROM ' . $qi('artist');
$statement = $adapter->query($sql);
$results = $statement->execute(array());

foreach ($results as $row) {
    var_export($row);
}
```

### TableGateway

```php
// where $adapter is an adapter object
$artistTable = new TableGateway('artist', $adapter);
$rowset = $artistTable->select(function (Select $select) {
    $select->where->like('name', 'Bar%');
});
$row = $rowset->current();
echo $row->name;
```

During my review of the code, which included assisting Ralph with testing, I
was impressed with the heavy level of de-coupling present, and how easily it
will allow us to support things like platform-specific SQL, custom rowsets, and
more.

For more details, [read the Zend\Db documentation](http://packages.zendframework.com/docs/latest/manual/en/zend.db.html).

Configuration
-------------

Configuration should be very fast. Interestingly, developers often also expect
configuration to support a multitude of features — key translation, section
inheritance, importing of additional configuration files, constant
substitution, compatibility with many configuration formats, and more. These
things tend to work in direct opposition to performance goals.

Several ZF2 community members decided to tackle these issues. Their goal was to
create a streamlined core for `Zend\Config`, but provide a variety of plugins
and filters to provide the rich features many users have come to expect. The
result is a very nice, de-coupled component.

Basic usage remains the same as it always has. However, without enabling any
optional features, you will not get things such as constant or token
substitution; to get that, you can use the new Processor API:

```php
// Get our config object; second argument tells the factory to return
// a Config object, vs. an array
$config = Zend\Config\Factory::fromFile($pathToConfigFile, true);

// Process values, substituting constant values whenever a defined constant name 
// is encountered
$constants = new Zend\Config\Processor\Constant();
$constants->process($config);

// Define some tokens to substitute
$tokens = new Zend\Config\Processor\Token();
$tokens->addToken('TOKEN', 'bar');
$tokens->process($config);
```

This API makes performance-intensive features explicitly opt-in, leaving the
core functionality intact and fast.

For more details, [read the Zend\Config documentation](http://packages.zendframework.com/docs/latest/manual/en/zend.config.html).

Fin!
----

I've only gone into depth on those features that had big iterations for the
beta release; plenty more work went into it — as I noted in the release
announcement, we handled around 200 pull requests over a 2 month period — this
is roughly double what we accomplished for beta2 over a similar timeframe! I'm
quite impressed and humbled by the spirit of the ZF2 community and
collaborators.

If you haven't tried Zend Framework 2 yet, please give it a spin! While there's
still work to be done, for many — most, potentially — use cases, the
functionality necessary is present and working very well. Trying it now, and
building real functionality on it now, is not only possible, but will allow you
to shape what ZF2 looks like when we're ready to go stable.

[Download it today!](http://packages.zendframework.com)
